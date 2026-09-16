<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'home_title' => 'nullable|string|max:100',
            'home_subtitle' => 'nullable|string|max:300',
            'footer_text' => 'nullable|string|max:500',
            'hero_image_base64' => 'nullable|string', // Contains base64 from cropper
        ]);

        $settingsToUpdate = [
            'home_title' => $request->home_title,
            'home_subtitle' => $request->home_subtitle,
            'footer_text' => $request->footer_text,
        ];

        if ($request->filled('hero_image_base64')) {
            $base64 = $request->hero_image_base64;
            
            // Extract the base64 data
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $data = substr($base64, strpos($base64, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);
                
                if ($data !== false) {
                    $filename = 'hero_' . time() . '.webp'; // Save as webp if possible or just original extension
                    $path = 'public/settings/' . $filename;
                    Storage::put($path, $data);
                    
                    // Delete old hero image if exists
                    $oldHero = Setting::where('key', 'home_hero_image')->first();
                    if ($oldHero && $oldHero->value) {
                        $oldPath = str_replace('/storage/', 'public/', $oldHero->value);
                        Storage::delete($oldPath);
                    }
                    
                    $settingsToUpdate['home_hero_image'] = '/storage/settings/' . $filename;
                }
            }
        }

        foreach ($settingsToUpdate as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('settings.index')->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
