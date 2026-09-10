<?php

namespace Tests\Feature;

use App\Models\LabItem;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_member_can_submit_loan_and_admin_approves_and_returns(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(['role' => 'member']);
        $item = LabItem::create([
            'code' => 'TEST-001',
            'name' => 'Barang Uji',
            'category' => 'Hardware',
            'stock' => 5,
        ]);

        $this->actingAs($member)
            ->post(route('loans.store'), [
                'item_id' => $item->id,
                'quantity' => 2,
                'borrow_date' => now()->toDateString(),
                'deadline' => now()->addDays(3)->toDateString(),
                'notes' => 'Praktikum',
            ])
            ->assertRedirect(route('loans.index'));

        $loan = Loan::first();
        $this->assertNotNull($loan->code);
        $this->assertSame(Loan::STATUS_PENDING, $loan->status);
        $this->assertSame(5, $item->fresh()->availableStock());

        $this->actingAs($admin)
            ->post(route('loans.approve', $loan))
            ->assertRedirect();

        $this->assertSame(Loan::STATUS_APPROVED, $loan->fresh()->status);
        $this->assertSame(3, $item->fresh()->availableStock());

        $this->actingAs($admin)
            ->post(route('loans.return', $loan))
            ->assertRedirect();

        $loan = $loan->fresh();
        $this->assertSame(Loan::STATUS_RETURNED, $loan->status);
        $this->assertNotNull($loan->returned_at);
        $this->assertSame(5, $item->fresh()->availableStock());
    }

    public function test_member_cannot_approve_loan(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $item = LabItem::create([
            'code' => 'TEST-002',
            'name' => 'Barang Uji 2',
            'category' => 'Hardware',
            'stock' => 2,
        ]);
        $loan = Loan::create([
            'user_id' => $member->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'borrow_date' => now()->toDateString(),
            'deadline' => now()->addDays(3)->toDateString(),
            'status' => Loan::STATUS_PENDING,
        ]);

        $this->actingAs($member)
            ->post(route('loans.approve', $loan))
            ->assertForbidden();
    }

    public function test_admin_can_manage_items(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('items.store'), [
                'code' => 'TEST-003',
                'name' => 'Projector',
                'category' => 'Hardware',
                'unit' => 'unit',
                'stock' => 3,
                'description' => 'Tes',
            ])
            ->assertRedirect(route('items.index'));

        $this->assertDatabaseHas('lab_items', ['code' => 'TEST-003']);
    }

    public function test_member_cannot_access_admin_item_management(): void
    {
        $member = User::factory()->create(['role' => 'member']);

        $this->actingAs($member)
            ->get(route('items.create'))
            ->assertForbidden();
    }

    public function test_member_can_cancel_own_pending_loan(): void
    {
        $member = User::factory()->create(['role' => 'member']);
        $item = LabItem::create([
            'code' => 'TEST-004',
            'name' => 'Barang Uji 4',
            'category' => 'Hardware',
            'stock' => 2,
        ]);
        $loan = Loan::create([
            'user_id' => $member->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'borrow_date' => now()->toDateString(),
            'deadline' => now()->addDays(3)->toDateString(),
            'status' => Loan::STATUS_PENDING,
        ]);

        $this->actingAs($member)
            ->delete(route('loans.destroy', $loan))
            ->assertRedirect();

        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
    }
}
