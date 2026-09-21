<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationAndBranchesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $cashier;

    private Branch $branch;

    private MenuItem $unlimitedMenuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@rasomandeh.com',
        ]);

        $this->cashier = User::factory()->create([
            'role' => 'cashier',
            'email' => 'cashier@rasomandeh.com',
        ]);

        $this->branch = Branch::create([
            'nama' => 'Raso Mandeh - Padang',
            'kota' => 'Padang',
            'alamat' => 'Jl. Khatib Sulaiman No. 10',
            'jam_buka' => '09:00 - 22:00',
            'kontak_whatsapp' => '6281234567899',
            'is_active' => true,
        ]);

        $this->unlimitedMenuItem = MenuItem::create([
            'nama' => 'Ayam Pop Spesial',
            'kategori' => 'ayam',
            'deskripsi' => 'Ayam pop lembut gurih khas Minang',
            'foto' => '/menu/ayam-pop.webp',
            'badge' => 'Signature',
            'rating' => 4.9,
            'is_active' => true,
            'stock_quantity' => null, // Stok tidak terbatas
            'availability_status' => 'tersedia',
        ]);
    }

    public function test_customer_can_order_unlimited_stock_item(): void
    {
        $response = $this->postJson('/api/v1/orders', [
            'branch_id' => $this->branch->id,
            'order_type' => 'dine_in',
            'table_number' => 'Meja 05',
            'customer_name' => 'Faris Pratama',
            'items' => [
                [
                    'menu_item_id' => $this->unlimitedMenuItem->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', [
            'branch_id' => $this->branch->id,
            'customer_name' => 'Faris Pratama',
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);
    }

    public function test_kasir_walkin_can_create_order_successfully(): void
    {
        $response = $this->actingAs($this->cashier)->postJson(route('orders.store'), [
            'customer_name' => 'Ibu Rahma',
            'customer_phone' => 'Walk-in Kasir',
            'service_type' => 'dine-in',
            'table_number' => 'Meja 03',
            'notes' => 'Diproses dari kasir (Walk-in)',
            'branch_id' => $this->branch->id,
            'source' => 'pos',
            'payment_status' => 'paid',
            'items' => [
                [
                    'id' => $this->unlimitedMenuItem->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Ibu Rahma',
            'status' => 'pending',
            'payment_status' => 'paid',
            'source' => 'pos',
        ]);
    }

    public function test_admin_branches_index_renders_all_branch_cards_properly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.branches.index'));

        $response->assertOk();
        $response->assertSee('Raso Mandeh - Padang');
        $response->assertSee('Padang');
        $response->assertSee('Jl. Khatib Sulaiman No. 10');
        $response->assertSee('09:00 - 22:00');
        $response->assertSee('Tambah Cabang Baru');
    }

    public function test_can_toggle_branch_active_status(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.branches.toggleActive', $this->branch->id));

        $response->assertRedirect();
        $this->assertFalse($this->branch->fresh()->is_active);

        $response2 = $this->actingAs($this->admin)->post(route('admin.branches.toggleActive', $this->branch->id));
        $response2->assertRedirect();
        $this->assertTrue($this->branch->fresh()->is_active);
    }
}
