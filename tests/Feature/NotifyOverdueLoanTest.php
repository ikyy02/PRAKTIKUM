<?php

namespace Tests\Feature;

use App\Models\LabItem;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NotifyOverdueLoanTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_whatsapp_notification_for_overdue_loan(): void
    {
        config()->set('services.fonnte.token', 'test-token');

        $member = User::factory()->create(['role' => 'member', 'phone' => '081234567890']);
        $item = LabItem::create([
            'code' => 'NOTIF-001',
            'name' => 'Router Mikrotik',
            'category' => 'Jaringan',
            'stock' => 3,
        ]);
        $loan = Loan::create([
            'code' => 'PMJ-0001',
            'user_id' => $member->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'borrow_date' => now()->subDays(5)->toDateString(),
            'deadline' => now()->subDay()->toDateString(),
            'status' => Loan::STATUS_APPROVED,
        ]);

        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true], 200),
        ]);

        $this->artisan('loans:notify-overdue')->assertSuccessful();

        $this->assertNotNull($loan->fresh()->overdue_notified_at);

        $request = Http::recorded()[0][0];
        $this->assertSame('6281234567890', $request['target']);
        $this->assertStringContainsString('PMJ-0001', $request['message']);
        $this->assertStringContainsString('Router Mikrotik', $request['message']);
    }

    public function test_command_skips_loan_already_notified(): void
    {
        config()->set('services.fonnte.token', 'test-token');

        $member = User::factory()->create(['role' => 'member', 'phone' => '081234567890']);
        $item = LabItem::create([
            'code' => 'NOTIF-002',
            'name' => 'Laptop',
            'category' => 'Hardware',
            'stock' => 2,
        ]);
        Loan::create([
            'code' => 'PMJ-0002',
            'user_id' => $member->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'borrow_date' => now()->subDays(5)->toDateString(),
            'deadline' => now()->subDay()->toDateString(),
            'status' => Loan::STATUS_APPROVED,
            'overdue_notified_at' => now(),
        ]);

        Http::fake();

        $this->artisan('loans:notify-overdue')->assertSuccessful();

        Http::assertNothingSent();
    }

    public function test_command_skips_unconfigured_token(): void
    {
        config()->set('services.fonnte.token', null);

        $this->artisan('loans:notify-overdue')->assertFailed();
    }
}
