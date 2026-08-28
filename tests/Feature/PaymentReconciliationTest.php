<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Owner;
use App\Models\Lot;
use App\Models\FunctionalUnit;
use App\Models\AccountMovement;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\LotHistoryCategory;
use App\Models\LotHistoryEventType;
use App\Models\Role;
use App\Services\BillingService;
use App\Services\ReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PaymentReconciliationTest extends TestCase
{
    use RefreshDatabase;

    protected $billingService;
    protected $reconciliationService;
    protected $adminUser;
    protected $ownerUser;
    protected $owner;
    protected $lot;
    protected $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->billingService = app(BillingService::class);
        $this->reconciliationService = app(ReconciliationService::class);

        // Seed basic settings/lookup tables
        Role::create(['name' => 'superadmin', 'display_name' => 'Superadmin']);
        Role::create(['name' => 'owner', 'display_name' => 'Owner']);
        LotHistoryCategory::create(['name' => 'finance', 'display_name' => 'Finance']);
        LotHistoryEventType::create(['name' => 'payment_approved', 'display_name' => 'Approved']);
        LotHistoryEventType::create(['name' => 'payment_rejected', 'display_name' => 'Rejected']);
        LotHistoryEventType::create(['name' => 'note_added', 'display_name' => 'Note']);

        // Create Users
        $this->adminUser = User::create([
            'name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'dni' => '99999999',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);
        $this->adminUser->roles()->attach(Role::where('name', 'superadmin')->first()->id);

        $this->owner = Owner::create([
            'name' => 'Juan',
            'last_name' => 'Perez',
            'dni' => '12345678',
            'cuit' => '20-12345678-9',
            'email' => 'juan@perez.com',
            'status' => 'active',
        ]);

        $this->lot = Lot::create([
            'number' => 45,
            'code' => 'LOT-045',
            'name' => 'Lote 45',
            'status' => 'active',
            'current_owner_id' => $this->owner->id,
            'balance' => 0.00,
        ]);

        $this->unit = FunctionalUnit::create([
            'lot_id' => $this->lot->id,
            'code' => 'LOT-045-UF',
            'name' => 'UF Lote 45',
            'balance' => 0.00,
        ]);

        $this->ownerUser = User::create([
            'name' => 'Juan',
            'last_name' => 'Perez',
            'email' => 'juan@perez.com',
            'dni' => '12345678',
            'status' => 'active',
            'password' => Hash::make('password'),
        ]);
        $this->ownerUser->roles()->attach(Role::where('name', 'owner')->first()->id);
    }

    /** @test */
    public function exact_reconciliation_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Create a debit movement (debt) of $150.00
        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(5)->toDateString(),
            'amount' => 150.00,
            'balance_after' => 150.00,
            'description' => 'Expensas Agosto',
        ]);
        $this->unit->update(['balance' => 150.00]);

        // Create a reported payment of $150.00
        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 150.00,
            'payment_method' => 'transfer',
            'operation_number' => '12345',
            'status' => 'pending',
        ]);

        // Reconcile
        $this->reconciliationService->reconcile($payment, [$debit->id => 150.00], $this->adminUser->id, 'manual');

        // Check variables after reconciliation
        $payment->refresh();
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals('manual', $payment->reconciliation_method);

        $this->unit->refresh();
        $this->assertEquals(0.00, $this->unit->balance); // balance is now 0

        // Check PaymentAllocation is active and matches
        $alloc = PaymentAllocation::where('payment_id', $payment->id)->first();
        $this->assertNotNull($alloc);
        $this->assertEquals(150.00, $alloc->allocated_amount);
        $this->assertEquals('active', $alloc->status);
    }

    /** @test */
    public function partial_payment_reconciliation_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Create debt of $200.00
        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(2)->toDateString(),
            'amount' => 200.00,
            'balance_after' => 200.00,
            'description' => 'Expensas Agosto',
        ]);
        $this->unit->update(['balance' => 200.00]);

        // Payment of $180.00
        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 180.00,
            'payment_method' => 'transfer',
            'operation_number' => '54321',
            'status' => 'pending',
        ]);

        // Reconcile partially
        $this->reconciliationService->reconcile($payment, [$debit->id => 180.00], $this->adminUser->id, 'manual');

        $this->unit->refresh();
        $this->assertEquals(20.00, $this->unit->balance); // outstanding unit balance is $20.00

        // Check allocation
        $alloc = PaymentAllocation::where('payment_id', $payment->id)->first();
        $this->assertEquals(180.00, $alloc->allocated_amount);
        $this->assertEquals(200.00, $alloc->previous_balance);
        $this->assertEquals(20.00, $alloc->posterior_balance);
    }

    /** @test */
    public function surplus_payment_creates_credit_balance()
    {
        $this->actingAs($this->adminUser);

        // Debt of $200.00
        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(2)->toDateString(),
            'amount' => 200.00,
            'balance_after' => 200.00,
            'description' => 'Expensas Agosto',
        ]);
        $this->unit->update(['balance' => 200.00]);

        // Payment of $220.00 (surplus of $20.00)
        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 220.00,
            'payment_method' => 'transfer',
            'operation_number' => '999888',
            'status' => 'pending',
        ]);

        // Allocate only $200.00 to the debt. The rest goes to surplus credit.
        $this->reconciliationService->reconcile($payment, [$debit->id => 200.00], $this->adminUser->id, 'manual');

        $this->unit->refresh();
        $this->assertEquals(-20.00, $this->unit->balance); // -$20.00 is surplus

        // Sum of allocations ($200) + surplus ($20) = payment ($220)
        $sumAllocations = PaymentAllocation::where('payment_id', $payment->id)->sum('allocated_amount');
        $surplus = abs($this->unit->balance); // since unit had 200.00, got 220.00 paid, so balance is -20.00
        $this->assertEquals(220.00, $sumAllocations + $surplus);
    }

    /** @test */
    public function allocation_to_multiple_debts_works_correctly()
    {
        $this->actingAs($this->adminUser);

        // Debt 1: $100.00
        $debit1 = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(30)->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas Julio',
        ]);
        // Debt 2: $150.00
        $debit2 = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(2)->toDateString(),
            'amount' => 150.00,
            'balance_after' => 250.00,
            'description' => 'Expensas Agosto',
        ]);
        $this->unit->update(['balance' => 250.00]);

        // Payment: $300.00 (surplus of $50.00)
        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 300.00,
            'status' => 'pending',
        ]);

        // Reconcile manually: $100.00 to July, $150.00 to August
        $allocations = [
            $debit1->id => 100.00,
            $debit2->id => 150.00
        ];
        $this->reconciliationService->reconcile($payment, $allocations, $this->adminUser->id, 'manual');

        $this->unit->refresh();
        $this->assertEquals(-50.00, $this->unit->balance); // -$50.00 is surplus

        $allocationsCount = PaymentAllocation::where('payment_id', $payment->id)->count();
        $this->assertEquals(2, $allocationsCount);
    }

    /** @test */
    public function double_reconciliation_fails_idempotency_check()
    {
        $this->actingAs($this->adminUser);

        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas',
        ]);
        $this->unit->update(['balance' => 100.00]);

        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        // 1st reconcile succeeds
        $this->assertTrue($this->reconciliationService->reconcile($payment, [$debit->id => 100.00], $this->adminUser->id, 'manual'));

        // 2nd reconcile throws Exception
        $this->expectException(\Exception::class);
        $this->reconciliationService->reconcile($payment, [$debit->id => 100.00], $this->adminUser->id, 'manual');
    }

    /** @test */
    public function reversion_rebuilds_balance_without_deleting_history()
    {
        $this->actingAs($this->adminUser);

        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 150.00,
            'balance_after' => 150.00,
            'description' => 'Expensas',
        ]);
        $this->unit->update(['balance' => 150.00]);

        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 150.00,
            'status' => 'pending',
        ]);

        // Reconcile
        $this->reconciliationService->reconcile($payment, [$debit->id => 150.00], $this->adminUser->id, 'manual');

        // Revert
        $this->reconciliationService->revert($payment, $this->adminUser->id, 'Error de digitacion');

        // Check payment is pending again
        $payment->refresh();
        $this->assertEquals('pending', $payment->status);
        $this->assertNotNull($payment->reverted_at);
        $this->assertEquals('Error de digitacion', $payment->reversion_reason);

        // Check unit balance is restored to $150.00
        $this->unit->refresh();
        $this->assertEquals(150.00, $this->unit->balance);

        // Check allocation is marked as reverted
        $alloc = PaymentAllocation::where('payment_id', $payment->id)->first();
        $this->assertEquals('reverted', $alloc->status);
        $this->assertEquals('Error de digitacion', $alloc->reversion_reason);

        // Check a debit contra-movement was created
        $reversalMove = AccountMovement::where('type', 'debit')
            ->where('description', 'like', '%REVERSIÓN PAGO%')
            ->first();
        $this->assertNotNull($reversalMove);
        $this->assertEquals(150.00, $reversalMove->amount);
    }

    /** @test */
    public function matching_scoring_prioritizes_structured_data_over_text()
    {
        // Debt 1: Lote 45 ( Juan Perez )
        $debit1 = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas',
        ]);

        // Payment 1: Has structured unit link + exact amount
        $payment1 = Payment::create([
            'functional_unit_id' => $this->unit->id,
            'amount' => 100.00,
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        // Payment 2: Has text-only link "Lote 45" + name, but no exact ID
        $payment2 = Payment::create([
            'amount' => 100.00,
            'payment_date' => now(),
            'notes' => 'Pago de expensas Juan Perez Lote 45',
            'status' => 'pending',
        ]);

        $match1 = $this->reconciliationService->calculateMatch($payment1);
        $match2 = $this->reconciliationService->calculateMatch($payment2);

        // Match 1 (structured) should have higher score than Match 2 (text-based)
        $this->assertGreaterThan($match2['score'], $match1['score']);
    }

    /** @test */
    public function matching_safety_rules_detect_ambiguity()
    {
        // Debt 1: Lote 45 (Juan Perez) - $100
        $debit1 = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas Julio',
        ]);

        // Debt 2: Lote 45 (Juan Perez) - $100 (diff period)
        $debit2 = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->subDays(5)->toDateString(),
            'amount' => 100.00,
            'balance_after' => 200.00,
            'description' => 'Expensas Agosto',
        ]);

        // Payment matches both deudas by lot and owner, and exact amount.
        // It should score high but be flagged as AMBIGUOUS (difference between candidates is 0), so not auto-reconcilable.
        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'amount' => 100.00,
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $matchResult = $this->reconciliationService->calculateMatch($payment);

        $this->assertFalse($matchResult['is_auto_reconcilable']); // Ambiguous!
    }

    /** @test */
    public function text_only_matching_does_not_auto_reconcile()
    {
        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas',
        ]);

        // Text matching gets points for name + amount + period, but lacks structural ID exact matches (lot_id, owner_id).
        // It must NOT auto-reconcile.
        $payment = Payment::create([
            'amount' => 100.00,
            'payment_date' => now(),
            'notes' => 'Expensas Agosto Juan Perez Lote 45',
            'status' => 'pending',
        ]);

        $matchResult = $this->reconciliationService->calculateMatch($payment);
        
        $this->assertFalse($matchResult['is_auto_reconcilable']);
    }

    /** @test */
    public function cannot_allocate_more_money_than_payment_amount()
    {
        $this->actingAs($this->adminUser);

        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 500.00,
            'balance_after' => 500.00,
            'description' => 'Expensas',
        ]);
        $this->unit->update(['balance' => 500.00]);

        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 100.00,
            'status' => 'pending',
        ]);

        // Attempting to allocate $200.00 when payment is only $100.00 should throw Exception
        $this->expectException(\Exception::class);
        $this->reconciliationService->reconcile($payment, [$debit->id => 200.00], $this->adminUser->id, 'manual');
    }

    /** @test */
    public function cannot_allocate_more_money_than_debt_remaining_amount()
    {
        $this->actingAs($this->adminUser);

        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.00,
            'balance_after' => 100.00,
            'description' => 'Expensas',
        ]);
        $this->unit->update(['balance' => 100.00]);

        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 150.00,
            'status' => 'pending',
        ]);

        // Attempting to allocate $120.00 when debt is only $100.00 should throw Exception
        $this->expectException(\Exception::class);
        $this->reconciliationService->reconcile($payment, [$debit->id => 120.00], $this->adminUser->id, 'manual');
    }

    /** @test */
    public function decimal_cents_rounding_is_safe()
    {
        $this->actingAs($this->adminUser);

        $debit = AccountMovement::create([
            'functional_unit_id' => $this->unit->id,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'amount' => 100.33,
            'balance_after' => 100.33,
            'description' => 'Expensas',
        ]);
        $this->unit->update(['balance' => 100.33]);

        $payment = Payment::create([
            'owner_id' => $this->owner->id,
            'lot_id' => $this->lot->id,
            'functional_unit_id' => $this->unit->id,
            'payment_date' => now(),
            'amount' => 100.33,
            'status' => 'pending',
        ]);

        $this->reconciliationService->reconcile($payment, [$debit->id => 100.33], $this->adminUser->id, 'manual');

        $this->unit->refresh();
        $this->assertEquals(0.00, $this->unit->balance);
    }
}
