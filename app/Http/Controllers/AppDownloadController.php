<?php

namespace App\Http\Controllers;

use App\Helpers\QrCodeHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppDownloadController extends Controller
{
    /**
     * Tampilkan halaman unduh aplikasi mobile khusus.
     */
    public function unduhPage(Request $request): View
    {
        $downloadUrl = Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk');
        $qrCodeSvg = QrCodeHelper::generate($downloadUrl, 320);

        return view('pages.download', compact('downloadUrl', 'qrCodeSvg'));
    }

    /**
     * Mengalirkan file APK Android untuk diunduh langsung oleh smartphone/browser.
     */
    public function downloadApk(): BinaryFileResponse|RedirectResponse
    {
        $candidatePaths = [
            public_path('downloads/rasa-mandeh.apk'),
            base_path('mobile/build/app/outputs/flutter-apk/app-release.apk'),
            base_path('mobile/build/app/outputs/flutter-apk/app-debug.apk'),
            base_path('build/app/outputs/flutter-apk/app-debug.apk'),
        ];

        $apkPath = null;
        foreach ($candidatePaths as $path) {
            if (file_exists($path)) {
                $apkPath = $path;
                break;
            }
        }

        if ($apkPath === null || ! file_exists($apkPath)) {
            return redirect()->route('home')->with('error', 'Berkas APK sedang dalam pembaruan tim teknis.');
        }

        $headers = [
            'Content-Type' => 'application/vnd.android.package-archive',
            'Content-Length' => (string) filesize($apkPath),
            'Cache-Control' => 'no-cache, must-revalidate',
        ];

        return response()->download($apkPath, 'rasa-mandeh-v1.0.0.apk', $headers);
    }

    /**
     * Menghasilkan QR Code SVG mentah untuk mengunduh APK secara langsung.
     */
    public function qrCode(): Response
    {
        $downloadUrl = Route::has('app.download.apk') ? route('app.download.apk') : url('/download/apk');
        $qrCodeDataUri = QrCodeHelper::generate($downloadUrl, 320);

        $svgContent = str_contains($qrCodeDataUri, ',')
            ? base64_decode(explode(',', $qrCodeDataUri)[1])
            : $qrCodeDataUri;

        return response($svgContent, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
