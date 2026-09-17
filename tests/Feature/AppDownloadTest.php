<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AppDownloadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure test public download dir and dummy apk exist for testing
        if (! File::isDirectory(public_path('downloads'))) {
            File::makeDirectory(public_path('downloads'), 0755, true);
        }
        if (! File::exists(public_path('downloads/rasa-mandeh.apk'))) {
            File::put(public_path('downloads/rasa-mandeh.apk'), 'DUMMY_APK_DATA_FOR_TEST');
        }
    }

    public function test_home_page_contains_app_download_section_and_links(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('download-app');
        $response->assertSee('Download APK Android');
        $response->assertSee('Cara Install di HP');
        $response->assertSee('Cara Pasang File APK di Smartphone Android');
    }

    public function test_apk_download_endpoint_returns_file_attachment(): void
    {
        $response = $this->get('/download/apk');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.android.package-archive');
        $this->assertStringContainsString('attachment; filename=rasa-mandeh-v1.0.0.apk', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_qr_code_endpoint_returns_svg_image(): void
    {
        $response = $this->get('/download/qr');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_dedicated_download_page_returns_successful_response(): void
    {
        $response = $this->get('/unduh-aplikasi');
        $response->assertStatus(200);
        $response->assertSee('Unduh Aplikasi Mobile');
        $response->assertSee('Download APK Android');
    }
}
