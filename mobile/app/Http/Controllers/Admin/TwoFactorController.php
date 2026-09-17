<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA Setup page.
     */
    public function setup()
    {
        $user = Auth::user();

        // If already confirmed, redirect back
        if ($user->two_factor_confirmed_at) {
            return redirect()->route('admin.profile')->with('info', '2FA is already active.');
        }

        $google2fa = new Google2FA;

        // Generate secret if doesn't exist
        if (! $user->two_factor_secret) {
            $user->two_factor_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        // Generate QR code URL
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        // Generate SVG string using chillerlan
        $options = new QROptions([
            'outputType' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::L,
            'svgViewBoxSize' => 300,
        ]);

        $qrcode = new QRCode($options);
        $qrImage = $qrcode->render($qrCodeUrl);

        return view('admin.profile.2fa-setup', [
            'secret' => $user->two_factor_secret,
            'qrImage' => $qrImage,
        ]);
    }

    /**
     * Confirm and activate 2FA
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $google2fa = new Google2FA;

        $valid = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $user->two_factor_confirmed_at = now();
            $user->save();

            return redirect()->route('admin.profile')->with('success', 'Otentikasi Dua Faktor (2FA) berhasil diaktifkan.');
        }

        return back()->with('error', 'Kode verifikasi tidak valid atau sudah kadaluarsa.');
    }

    /**
     * Disable 2FA
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return back()->with('success', 'Otentikasi Dua Faktor (2FA) berhasil dinonaktifkan.');
    }
}
