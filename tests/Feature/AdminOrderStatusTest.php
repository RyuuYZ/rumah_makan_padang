<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function createBranch(): Branch
    {
        return Branch::create([
            'nama' => 'Cabang Tebet',
            'alamat' => 'Jl. Tebet Raya No. 10',
            'kota' => 'Jakarta Selatan',
            'telepon' => '081234567890',
            'jam_buka' => '09:00',
            'jam_tutup' => '22:00',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_order_status_via_json()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@rasomandeh.com',
        ]);

        $branch = $this->createBranch();

        $order = Order::create([
            'branch_id' => $branch->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '08123456789',
            'status' => 'pending',
            'order_type' => 'dine_in',
            'method' => 'dine-in',
            'source' => 'pos',
            'total' => 50000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.orders.updateStatus', $order->id), [
            'status' => 'cooking',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'status' => 'cooking',
                    'status_label' => 'Cooking',
                ],
                'pending_count' => 0,
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cooking',
        ]);
    }

    public function test_admin_cannot_update_order_status_with_invalid_value()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@rasomandeh.com',
        ]);

        $branch = $this->createBranch();

        $order = Order::create([
            'branch_id' => $branch->id,
            'customer_name' => 'Budi Santoso',
            'status' => 'pending',
            'order_type' => 'dine_in',
            'method' => 'dine-in',
            'source' => 'pos',
            'total' => 50000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.orders.updateStatus', $order->id), [
            'status' => 'invalid_status_type',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_unauthenticated_user_cannot_update_order_status()
    {
        $branch = $this->createBranch();

        $order = Order::create([
            'branch_id' => $branch->id,
            'customer_name' => 'Budi Santoso',
            'status' => 'pending',
            'order_type' => 'dine_in',
            'method' => 'dine-in',
            'source' => 'pos',
            'total' => 50000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->postJson(route('admin.orders.updateStatus', $order->id), [
            'status' => 'cooking',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_update_order_status_via_traditional_form()
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@rasomandeh.com',
        ]);

        $branch = $this->createBranch();

        $order = Order::create([
            'branch_id' => $branch->id,
            'customer_name' => 'Budi Santoso',
            'status' => 'pending',
            'order_type' => 'dine_in',
            'method' => 'dine-in',
            'source' => 'pos',
            'total' => 50000,
            'payment_status' => 'unpaid',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.updateStatus', $order->id), [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'completed',
        ]);
    }
}
