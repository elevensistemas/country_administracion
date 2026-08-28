<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Lot;
use App\Models\CommonArea;
use App\Models\Reservation;
use App\Models\TicketCategory;
use App\Models\Ticket;
use App\Models\LotHistoryEvent;
use App\Models\BillingPeriod;
use App\Models\Import;
use App\Models\ImportRow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationAndTicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default roles or database dependencies
        $this->artisan('db:seed');
    }

    private function assignRoleToUser($user, $roleName)
    {
        $role = Role::firstOrCreate(['name' => $roleName], ['display_name' => ucfirst($roleName)]);
        $user->roles()->syncWithoutDetaching([$role->id]);
    }

    public function test_resident_can_create_exclusive_reservation()
    {
        // 1. Setup models
        $resident = User::factory()->create([
            'relationship_type' => 'owner',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($resident, 'owner');

        $lot = Lot::create([
            'number' => '999',
            'code' => 'L999',
            'balance' => 0.00,
        ]);
        
        // Associate resident to lot through functional unit
        $fu = $lot->functionalUnits()->create([
            'name' => 'UF 999',
            'code' => 'UF999',
            'balance' => 0.00,
        ]);
        $resident->functionalUnits()->attach($fu);

        $commonArea = CommonArea::create([
            'name' => 'Quincho',
            'price' => 5000.00,
            'schedule_start' => '09:00:00',
            'schedule_end' => '23:00:00',
            'duration_minutes' => 180,
            'requires_approval' => false,
        ]);

        // Set active lot in session
        $this->actingAs($resident);
        session(['active_lot_id' => $lot->id]);

        // 2. Submit exclusive reservation request
        $response = $this->post(route('owner.reservations.store'), [
            'common_area_id' => $commonArea->id,
            'reservation_date' => now()->addDays(2)->toDateString(),
            'start_time' => '13:00',
            'end_time' => '18:00',
            'is_exclusive' => '1',
            'accept_rules' => '1',
        ]);

        $response->assertRedirect(route('owner.reservations.index'));
        $this->assertDatabaseHas('reservations', [
            'common_area_id' => $commonArea->id,
            'lot_id' => $lot->id,
            'is_exclusive' => true,
            'price' => 0.00,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_confirm_exclusive_reservation_with_custom_price()
    {
        // 1. Setup models
        $admin = User::factory()->create([
            'relationship_type' => 'admin',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($admin, 'admin');

        $lot = Lot::create([
            'number' => '999',
            'code' => 'L999',
            'balance' => 0.00,
        ]);
        $fu = $lot->functionalUnits()->create([
            'name' => 'UF 999',
            'code' => 'UF999',
            'balance' => 0.00,
        ]);

        $commonArea = CommonArea::create([
            'name' => 'Quincho',
            'price' => 5000.00,
            'schedule_start' => '09:00:00',
            'schedule_end' => '23:00:00',
            'duration_minutes' => 180,
            'requires_approval' => false,
        ]);

        $reservation = Reservation::create([
            'common_area_id' => $commonArea->id,
            'lot_id' => $lot->id,
            'user_id' => $admin->id,
            'reservation_date' => now()->addDays(2)->toDateString(),
            'start_time' => '13:00:00',
            'end_time' => '18:00:00',
            'price' => 0.00,
            'charge_to_expenses' => true,
            'status' => 'pending',
            'is_exclusive' => true,
        ]);

        // 2. Admin confirms reservation and sets custom price
        $response = $this->actingAs($admin)->post(route('admin.reservations.status', $reservation), [
            'status' => 'confirmed',
            'price' => 7500.00,
        ]);

        $response->assertRedirect();
        
        // Assert reservation details updated
        $reservation->refresh();
        $this->assertEquals('confirmed', $reservation->status);
        $this->assertEquals(7500.00, $reservation->price);

        // Assert accounting impact
        $fu->refresh();
        $this->assertEquals(7500.00, $fu->balance);

        $this->assertDatabaseHas('account_movements', [
            'functional_unit_id' => $fu->id,
            'type' => 'debit',
            'amount' => 7500.00,
            'related_model_type' => get_class($reservation),
            'related_model_id' => $reservation->id,
        ]);
    }

    public function test_admin_can_create_ticket_manually()
    {
        // 1. Setup models
        $admin = User::factory()->create([
            'relationship_type' => 'admin',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($admin, 'admin');

        $resident = User::factory()->create([
            'relationship_type' => 'owner',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($resident, 'owner');

        $lot = Lot::create([
            'number' => '999',
            'code' => 'L999',
            'balance' => 0.00,
        ]);
        $fu = $lot->functionalUnits()->create([
            'name' => 'UF 999',
            'code' => 'UF999',
            'balance' => 0.00,
        ]);
        $resident->functionalUnits()->attach($fu);

        $category = TicketCategory::first() ?? TicketCategory::create(['name' => 'admin', 'display_name' => 'Administración']);

        // 2. Admin registers manual ticket
        $response = $this->actingAs($admin)->post(route('admin.tickets.store'), [
            'lot_id' => $lot->id,
            'user_id' => $resident->id,
            'category_id' => $category->id,
            'title' => 'Incidente de luminaria',
            'description' => 'La luminaria pública no enciende.',
            'priority' => 'high',
            'source_channel' => 'phone',
        ]);

        $response->assertRedirect(route('admin.tickets.index'));
        
        $this->assertDatabaseHas('tickets', [
            'lot_id' => $lot->id,
            'user_id' => $resident->id,
            'category_id' => $category->id,
            'title' => 'Incidente de luminaria',
            'priority' => 'high',
            'source_channel' => 'phone',
            'status' => 'open',
        ]);

        // Check history event log
        $this->assertDatabaseHas('lot_history_events', [
            'lot_id' => $lot->id,
            'title' => 'Reclamo Registrado por Administración',
        ]);
    }

    public function test_admin_can_import_expenses_from_csv()
    {
        // 1. Setup models
        $admin = User::factory()->create([
            'relationship_type' => 'admin',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($admin, 'admin');

        $lot1 = Lot::create([
            'number' => '9991',
            'code' => 'L9991',
            'balance' => 0.00,
        ]);
        $fu1 = $lot1->functionalUnits()->create([
            'name' => 'UF 9991',
            'code' => 'UF9991',
            'balance' => 0.00,
        ]);

        $lot2 = Lot::create([
            'number' => '9992',
            'code' => 'L9992',
            'balance' => 0.00,
        ]);
        $fu2 = $lot2->functionalUnits()->create([
            'name' => 'UF 9992',
            'code' => 'UF9992',
            'balance' => 0.00,
        ]);

        $period = BillingPeriod::create([
            'period' => '2026-09',
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-30',
            'status' => 'draft',
        ]);

        // Create import record
        $import = Import::create([
            'type' => 'expenses',
            'filename' => 'expensas.csv',
            'file_path' => 'imports/dummy.csv',
            'status' => 'pending',
            'total_rows' => 2,
            'valid_rows' => 2,
            'invalid_rows' => 0,
            'user_id' => $admin->id,
        ]);

        // Create import row data
        ImportRow::create([
            'import_id' => $import->id,
            'row_number' => 1,
            'data' => [
                'lote' => '9991',
                'monto_expensa' => '18500.00',
                'monto_fondo_reserva' => '4000.00',
                'billing_period_id' => $period->id,
            ],
            'status' => 'pending',
        ]);

        ImportRow::create([
            'import_id' => $import->id,
            'row_number' => 2,
            'data' => [
                'lote' => '9992',
                'monto_expensa' => '22000.00',
                'monto_fondo_reserva' => '5000.00',
                'billing_period_id' => $period->id,
            ],
            'status' => 'pending',
        ]);

        // 2. Action: call process endpoint
        $response = $this->actingAs($admin)->post(route('admin.imports.process', $import));

        $response->assertRedirect(route('admin.imports.index'));

        // 3. Asserts
        $import->refresh();
        $this->assertEquals('completed', $import->status);
        $this->assertEquals(2, $import->valid_rows);

        // Check lot 1 expense and accounting
        $this->assertDatabaseHas('expenses', [
            'billing_period_id' => $period->id,
            'functional_unit_id' => $fu1->id,
            'capital_amount' => 22500.00, // 18500 + 4000
            'total_amount' => 22500.00,
            'status' => 'draft',
        ]);

        $fu1->refresh();
        $this->assertEquals(22500.00, $fu1->balance);

        // Check lot 2 expense and accounting
        $this->assertDatabaseHas('expenses', [
            'billing_period_id' => $period->id,
            'functional_unit_id' => $fu2->id,
            'capital_amount' => 27000.00, // 22000 + 5000
            'total_amount' => 27000.00,
        ]);

        $fu2->refresh();
        $this->assertEquals(27000.00, $fu2->balance);
    }
}
