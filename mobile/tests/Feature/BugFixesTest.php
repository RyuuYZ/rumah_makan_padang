<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchMenuPrice;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugFixesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Branch $branch;

    private MenuItem $menuItem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@rasomandeh.com',
        ]);

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

        BranchMenuPrice::create([
            'branch_id' => $this->branch->id,
            'menu_item_id' => $this->menuItem->id,
            'harga' => 35000,
            'is_available' => true,
        ]);
    }

    /**
     * Phase 1: POS Scanner returns correct item nama, subtotal, and branch nama.
     */
    public function test_pos_scanner_find_order_returns_correct_item_names_and_subtotals(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'order_type' => 'dine_in',
            'table_number' => 'Meja 05',
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'total' => 70000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'source' => 'customer_web',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $this->menuItem->id,
            'quantity' => 2,
            'price' => 35000,
            'notes' => 'Pedas sedang',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.pos.findOrder'), [
            'order_code' => $order->order_number,
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('order.items.0.name', 'Rendang Daging Autentik');
        $response->assertJsonPath('order.items.0.subtotal_formatted', 'Rp 70.000');
        $response->assertJsonPath('order.branch.name', 'Raso Mandeh - Padang');
        $response->assertJsonPath('order.payment_status', 'unpaid');
    }

    /**
     * Phase 1: Order completion synchronizes payment_status = paid and frees table.
     */
    public function test_order_completion_synchronizes_payment_and_releases_table(): void
    {
        $table = Table::create([
            'branch_id' => $this->branch->id,
            'table_number' => 'Meja 08',
            'capacity' => 4,
            'status' => 'occupied',
            'is_active' => true,
        ]);

        $order = Order::create([
            'branch_id' => $this->branch->id,
            'order_type' => 'dine_in',
            'table_number' => 'Meja 08',
            'customer_name' => 'Faisal',
            'total' => 35000,
            'status' => 'cooking',
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->admin)->postJson(route('admin.orders.updateStatus', $order->id), [
            'status' => 'completed',
        ]);

        $response->assertOk();
        $this->assertEquals('completed', $order->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals($this->admin->id, $order->fresh()->cashier_id);
        $this->assertEquals('available', $table->fresh()->status);

        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('completed', $payment->status);
        $this->assertEquals(35000, (int) $payment->amount);
    }

    /**
     * Phase 2: Menu deletion uses SoftDeletes, preserving historical order items.
     */
    public function test_menu_item_deletion_soft_deletes_and_preserves_order_history(): void
    {
        $order = Order::create([
            'branch_id' => $this->branch->id,
            'total' => 35000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        $orderItem = OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $this->menuItem->id,
            'quantity' => 1,
            'price' => 35000,
        ]);

        // Delete menu item via admin route
        $response = $this->actingAs($this->admin)->delete(route('admin.menu.destroy', $this->menuItem->id));
        $response->assertRedirect(route('admin.menu.index'));

        // Assert soft deleted
        $this->assertTrue($this->menuItem->fresh()->trashed());

        // Assert order item still exists and resolves menuItem relationship via withTrashed
        $this->assertDatabaseHas('order_items', ['id' => $orderItem->id]);
        $this->assertEquals('Rendang Daging Autentik', $orderItem->fresh()->menuItem->nama);
    }

    /**
     * Phase 3: New branch creation inherits existing menu prices and generates 20 tables.
     */
    public function test_new_branch_inherits_existing_menu_prices_and_generates_tables(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.branches.store'), [
            'nama' => 'Raso Mandeh - Solo',
            'kota' => 'Solo',
            'alamat' => 'Jl. Slamet Riyadi No. 50',
            'jam_buka' => '09:00 - 22:00',
            'kontak_whatsapp' => '6281234567800',
        ]);

        $response->assertRedirect(route('admin.branches.index'));

        $newBranch = Branch::where('kota', 'Solo')->first();
        $this->assertNotNull($newBranch);

        // Assert menu price was copied from existing branch (35000, NOT hardcoded 25000)
        $branchPrice = BranchMenuPrice::where('branch_id', $newBranch->id)
            ->where('menu_item_id', $this->menuItem->id)
            ->first();

        $this->assertNotNull($branchPrice);
        $this->assertEquals(35000, (int) $branchPrice->harga);

        // Assert 20 tables were automatically generated
        $tableCount = Table::where('branch_id', $newBranch->id)->count();
        $this->assertEquals(20, $tableCount);
    }

    /**
     * Phase 3: Dashboard revenue only counts completed or paid non-cancelled orders.
     */
    public function test_dashboard_revenue_only_counts_completed_or_paid_orders(): void
    {
        // 1. Completed order (50000) -> Should count
        Order::create([
            'branch_id' => $this->branch->id,
            'total' => 50000,
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        // 2. Ready order but paid (40000) -> Should count
        Order::create([
            'branch_id' => $this->branch->id,
            'total' => 40000,
            'status' => 'ready',
            'payment_status' => 'paid',
        ]);

        // 3. Pending unpaid order (30000) -> Should NOT count
        Order::create([
            'branch_id' => $this->branch->id,
            'total' => 30000,
            'status' => 'pending',
            'payment_status' => 'unpaid',
        ]);

        // 4. Cancelled order (20000) -> Should NOT count
        Order::create([
            'branch_id' => $this->branch->id,
            'total' => 20000,
            'status' => 'cancelled',
            'payment_status' => 'voided',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertViewHas('totalRevenue', 90000);
    }

    /**
     * Phase 4: Customer order checkout assigns the selected branch ID accurately.
     */
    public function test_api_order_creation_with_specific_branch(): void
    {
        $branchBandung = Branch::create([
            'nama' => 'Raso Mandeh - Bandung',
            'kota' => 'Bandung',
            'alamat' => 'Jl. Riau No. 10',
            'jam_buka' => '09:00 - 22:00',
            'is_active' => true,
        ]);

        BranchMenuPrice::create([
            'branch_id' => $branchBandung->id,
            'menu_item_id' => $this->menuItem->id,
            'harga' => 36000,
            'is_available' => true,
        ]);

        $response = $this->postJson('/api/v1/orders', [
            'branch_id' => $branchBandung->id,
            'order_type' => 'dine_in',
            'customer_name' => 'Andi Wijaya',
            'items' => [
                [
                    'menu_item_id' => $this->menuItem->id,
                    'quantity' => 2,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        $orderId = $response->json('data.order_id');
        $order = Order::find($orderId);
        $this->assertEquals($branchBandung->id, $order->branch_id);
        $this->assertEquals(72000, (int) $order->total);
    }
}
