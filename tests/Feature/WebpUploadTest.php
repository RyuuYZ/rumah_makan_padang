<?php

namespace Tests\Feature;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use App\Services\WebpUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class WebpUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_webp_upload_service_converts_image_to_webp()
    {
        $service = new WebpUploadService;
        $fakeFile = UploadedFile::fake()->image('test_dish.png', 100, 100);

        $path = $service->uploadAndConvertToWebp($fakeFile, 'menu');

        $this->assertStringEndsWith('.webp', $path);
        $this->assertFileExists(public_path(ltrim($path, '/')));

        // Cleanup
        if (file_exists(public_path(ltrim($path, '/')))) {
            unlink(public_path(ltrim($path, '/')));
        }
    }

    public function test_admin_can_upload_menu_image_in_webp_format()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@rasomandeh.com',
        ]);

        $category = MenuCategory::firstOrCreate(['slug' => 'daging'], ['nama' => 'Daging Sapi']);
        $fakeFile = UploadedFile::fake()->image('gulai_tunjang.jpg', 200, 200);

        $response = $this->actingAs($admin)->post(route('admin.menu.store'), [
            'nama' => 'Gulai Tunjang Special',
            'menu_category_id' => $category->id,
            'kategori' => 'daging',
            'deskripsi' => 'Gulai tunjang lezat',
            'harga' => 35000,
            'foto_file' => $fakeFile,
        ]);

        $response->assertRedirect(route('admin.menu.index'));

        $menuItem = MenuItem::where('nama', 'Gulai Tunjang Special')->first();
        $this->assertNotNull($menuItem);
        $this->assertStringEndsWith('.webp', $menuItem->foto);
        $this->assertFileExists(public_path(ltrim($menuItem->foto, '/')));

        // Cleanup
        if (file_exists(public_path(ltrim($menuItem->foto, '/')))) {
            unlink(public_path(ltrim($menuItem->foto, '/')));
        }
    }

    public function test_api_upload_image_returns_webp_format()
    {
        $admin = User::factory()->admin()->create();
        $fakeFile = UploadedFile::fake()->image('rendang_sapi.png', 150, 150);

        $response = $this->actingAs($admin)->postJson('/api/v1/menu-items/upload-image', [
            'image' => $fakeFile,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'format' => 'webp',
                ],
            ]);

        $path = $response->json('data.path');
        $this->assertStringEndsWith('.webp', $path);

        // Cleanup
        if (file_exists(public_path(ltrim($path, '/')))) {
            unlink(public_path(ltrim($path, '/')));
        }
    }
}
