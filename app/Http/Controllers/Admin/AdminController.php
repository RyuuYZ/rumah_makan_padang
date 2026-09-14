<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|string',
        ]);

        $image_parts = explode(';base64,', $request->photo);
        if (count($image_parts) < 2) {
            return response()->json(['success' => false, 'message' => 'Format foto tidak valid. Pastikan format Base64 benar.'], 400);
        }

        $image_type_aux = explode('image/', $image_parts[0]);
        $image_type = isset($image_type_aux[1]) ? $image_type_aux[1] : 'png';
        $image_base64 = base64_decode($image_parts[1]);

        $fileName = 'profile-photos/'.uniqid().'.png';

        Storage::disk('public')->put($fileName, $image_base64);

        $user = auth()->user();

        // Delete old photo
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->profile_photo_path = $fileName;
        $user->save();

        return response()->json(['success' => true, 'photo_url' => asset('storage/'.$fileName)]);
    }
}
