<?php

namespace App\Http\Controllers;

use App\Helpers\QrCodeHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AppDownloadController extends Controller
{
    /**
     * Tampilkan halaman unduh aplikasi mobile khusus.
     */
    public function unduhPage(Request $request): View
    {
        $downloadUrl = route('app.download.apk');
        $qrCodeSvg = QrCodeHelper::generate($downloadUrl, 320);

        return view('pages.download', compact('downloadUrl', 'qrCodeSvg'));
    }

    /**
     * Mengalirkan file APK Android untuk diunduh langsung oleh smartphone/browser.
     */
    public function downloadApk(): BinaryFileResponse|RedirectResponse
    {
        $primaryPath = public_path('downloads/rasa-mandeh.apk');
        $fallbackPath = base_path('build/app/outputs/flutter-apk/app-debug.apk');

        $apkPath = file_exists($primaryPath) ? $primaryPath : (file_exists($fallbackPath) ? $fallbackPath : null);

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
     * Menghasilkan QR Code SVG base64 untuk mengunduh APK secara langsung.
     */
    public function qrCode(): Response
    {
        $downloadUrl = route('app.download.apk');
        $qrCodeSvg = QrCodeHelper::generate($downloadUrl, 320);

        return response($qrCodeSvg, 200, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }
}
