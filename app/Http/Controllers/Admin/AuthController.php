<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Handle the admin login attempt with Dynamic Rate Limiting.
     */
    public function login(Request $request)
    {
        $this->checkRateLimit($request);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi harus diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::validate($credentials)) {
            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            if (! $user->is_active) {
                return back()->withErrors(['email' => 'Akun Anda dinonaktifkan. Silakan hubungi pengelola.']);
            }

            if ($user->two_factor_confirmed_at) {
                // User has 2FA enabled, don't login fully yet
                $request->session()->put([
                    '2fa_user_id' => $user->id,
                    '2fa_remember' => $remember,
                ]);
                $this->clearRateLimit($request);

                return back()->with('show_2fa_modal', true);
            }

            // Normal login if no 2FA
            Auth::login($user, $remember);
            $this->clearRateLimit($request);
            $request->session()->regenerate();

            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'Login',
                'description' => 'Berhasil masuk melalui form login (Password).',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Selamat datang kembali, '.$user->name.'!');
        }

        $this->hitRateLimit($request);

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Handle the admin QR Login attempt.
     */
    public function qrLogin(Request $request)
    {
        $request->validate([
            'login_token' => 'required|string',
        ]);

        $user = User::where('login_token', $request->login_token)->first();

        if ($user) {
            $user->update(['login_token' => null]);
            
            if ($user->two_factor_confirmed_at) {
                $request->session()->put([
                    '2fa_user_id' => $user->id,
                    '2fa_remember' => true,
                ]);
                $this->clearRateLimit($request);
                return response()->json([
                    'success' => true,
                    'redirect' => route('admin.login.2fa'),
                ]);
            }

            Auth::login($user, true);
            $request->session()->regenerate();

            // Clear rate limits if they had any for their email, though IP might be different
            // We'll just let it expire on its own or clear it based on email
            $failsKey = 'login_fails:'.Str::lower($user->email).'|'.$request->ip();
            $lockKey = 'login_locked:'.Str::lower($user->email).'|'.$request->ip();
            cache()->forget($failsKey);
            cache()->forget($lockKey);

            return response()->json([
                'success' => true,
                'redirect' => route('admin.dashboard'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token QR tidak valid atau sudah kadaluarsa.',
        ], 401);
    }

    /**
     * Show the 2FA verification page.
     */
    public function show2faVerify(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('admin.auth.2fa');
    }

    /**
     * Verify 2FA OTP and complete login.
     */
    public function verify2fa(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|string|size:6']);

        $user = User::find($request->session()->get('2fa_user_id'));
        if (! $user || ! $user->two_factor_secret) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi tidak valid.']);
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $rateLimitKey = '2fa_attempts:'.$user->id;

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->with('error', 'Terlalu banyak percobaan. Silakan coba lagi dalam '.ceil($seconds / 60).' menit.');
        }

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            RateLimiter::clear($rateLimitKey);
            Auth::login($user, $request->session()->get('2fa_remember', false));
            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            $request->session()->regenerate();

            SystemLog::create([
                'user_id' => $user->id,
                'action' => 'Login',
                'description' => 'Berhasil masuk melalui verifikasi Dua Langkah (2FA OTP).',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->intended(route('admin.dashboard'))->with('success', 'Verifikasi berhasil. Selamat datang kembali, '.Auth::user()->name.'!');
        }

        RateLimiter::hit($rateLimitKey, 3600); // Lock out for 1 hour after 5 fails

        return back()->with('error', 'Kode OTP tidak valid atau sudah kadaluarsa.');
    }

    /**
     * Cancel 2FA login process.
     */
    public function cancel2fa(Request $request)
    {
        $request->session()->forget(['2fa_user_id', '2fa_remember']);

        return redirect()->route('login');
    }

    /**
     * Check if user is locked out due to rate limit.
     */
    protected function checkRateLimit(Request $request)
    {
        $lockKey = 'login_locked:'.Str::lower($request->input('email', '')).'|'.$request->ip();

        if (cache()->has($lockKey)) {
            $unlockTime = cache()->get($lockKey);
            $seconds = $unlockTime - time();

            if ($seconds > 0) {
                throw ValidationException::withMessages([
                    'email' => 'Terlalu banyak percobaan login salah. Akun dikunci sementara. Silakan coba lagi dalam '.ceil($seconds / 60).' menit.',
                ]);
            } else {
                cache()->forget($lockKey);
            }
        }
    }

    /**
     * Hit the rate limiter on failed attempt.
     */
    protected function hitRateLimit(Request $request)
    {
        $failsKey = 'login_fails:'.Str::lower($request->input('email', '')).'|'.$request->ip();
        $lockKey = 'login_locked:'.Str::lower($request->input('email', '')).'|'.$request->ip();

        if (! cache()->has($failsKey)) {
            cache()->put($failsKey, 0, now()->addHours(6));
        }

        $fails = cache()->increment($failsKey);

        if ($fails >= 5) {
            $multiplier = $fails - 5;
            if ($multiplier > 4) {
                $multiplier = 4;
            } // Max 5 hours

            $minutes = pow(5, $multiplier);
            if ($minutes > 300) {
                $minutes = 300;
            }

            cache()->put($lockKey, time() + ($minutes * 60), now()->addMinutes($minutes));
        }
    }

    /**
     * Clear the rate limiter on success.
     */
    protected function clearRateLimit(Request $request)
    {
        $failsKey = 'login_fails:'.Str::lower($request->input('email', '')).'|'.$request->ip();
        $lockKey = 'login_locked:'.Str::lower($request->input('email', '')).'|'.$request->ip();

        cache()->forget($failsKey);
        cache()->forget($lockKey);
    }

    /**
     * Handle the admin logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
