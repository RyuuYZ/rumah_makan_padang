<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsDisplayTest extends TestCase
{
    use RefreshDatabase;

    private Branch $branch;

    private MenuItem $menuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::create([
            'nama' => 'Raso Mandeh - Padang',
            'kota' => 'Padang',
            'alamat' => 'Jl. Khatib Sulaiman No. 10',
            'jam_buka' => '09:00 - 22:00',
            'kontak_whatsapp' => '6281234567899',
            'is_active' => true,
        ]);

        $this->menuItem = MenuItem::create([
            'nama' => 'Rendang Daging Autentik',
            'kategori' => 'daging',
            'deskripsi' => 'Rendang daging sapi khas Minang',
            'foto' => '/menu/rendang.webp',
            'badge' => 'Signature',
            'rating' => 4.9,
            'is_active' => true,
        ]);
    }

    public function test_approved_reviews_are_displayed_on_home_page(): void
    {
        Review::create([
            'nama_pelanggan' => 'Siti Rahma',
            'rating' => 5,
            'komentar' => 'Makanannya lezat sekali dan otentik!',
            'is_approved' => true,
            'is_pinned' => false,
            'branch_id' => $this->branch->id,
            'menu_item_id' => $this->menuItem->id,
        ]);

        Review::create([
            'nama_pelanggan' => 'Pending Reviewer',
            'rating' => 4,
            'komentar' => 'Ulasan ini masih menunggu persetujuan.',
            'is_approved' => false,
            'is_pinned' => false,
            'branch_id' => $this->branch->id,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Siti Rahma');
        $response->assertSee('Makanannya lezat sekali dan otentik!');
        $response->assertDontSee('Pending Reviewer');
        $response->assertDontSee('Ulasan ini masih menunggu persetujuan.');
    }

    public function test_pinned_reviews_appear_first(): void
    {
        $oldPinnedReview = Review::create([
            'nama_pelanggan' => 'Pelanggan Pilihan',
            'rating' => 5,
            'komentar' => 'Ulasan favorit yang disematkan!',
            'is_approved' => true,
            'is_pinned' => true,
            'branch_id' => $this->branch->id,
            'created_at' => now()->subDays(5),
        ]);

        $newerRegularReview = Review::create([
            'nama_pelanggan' => 'Pelanggan Baru',
            'rating' => 5,
            'komentar' => 'Ulasan reguler terbaru.',
            'is_approved' => true,
            'is_pinned' => false,
            'branch_id' => $this->branch->id,
            'created_at' => now(),
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $reviews = $response->viewData('reviews');
        $this->assertCount(2, $reviews);
        $this->assertEquals('Pelanggan Pilihan', $reviews->first()->nama_pelanggan);
        $this->assertEquals('Pelanggan Baru', $reviews->last()->nama_pelanggan);
    }

    public function test_empty_state_rendered_when_no_approved_reviews(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Belum ada ulasan yang ditampilkan');
    }
}
