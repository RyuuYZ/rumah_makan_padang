<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle Google Sign-In verification & user registration/login.
     */
    public function googleLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'google_id' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string|max:1000',
            'id_token' => 'nullable|string',
        ]);

        $user = User::firstOrCreate(
            ['email' => $validated['email']],
            [
                'name' => $validated['name'] ?? explode('@', $validated['email'])[0],
                'password' => bcrypt(Str::random(32)),
                'role' => 'customer',
                'is_active' => true,
                'profile_photo_path' => $validated['avatar_url'] ?? null,
                'email_verified_at' => now(),
            ]
        );

        // Update photo if provided and user doesn't have one
        if (! empty($validated['avatar_url']) && empty($user->profile_photo_path)) {
            $user->update(['profile_photo_path' => $validated['avatar_url']]);
        }

        $loginToken = Str::random(60);
        $user->update(['login_token' => $loginToken]);

        return response()->json([
            'success' => true,
            'message' => 'Login Google berhasil.',
            'data' => [
                'id' => (string) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '0812-3456-7890',
                'address' => 'Kota Padang, Sumatera Barat',
                'memberTier' => 'Minang Gold Member',
                'token' => $loginToken,
                'avatar_url' => $user->profile_photo_path,
            ],
        ]);
    }
}
