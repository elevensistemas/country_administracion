<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Lot;
use App\Models\GuestAuthorization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestAuthorizationTest extends TestCase
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

    public function test_owner_can_access_guest_pages()
    {
        $owner = User::factory()->create([
            'relationship_type' => 'owner',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($owner, 'owner');

        $this->actingAs($owner);

        $response = $this->get(route('owner.guests.index'));
        $response->assertStatus(200);

        $response = $this->get(route('owner.guests.create'));
        $response->assertStatus(200);
    }

    public function test_owner_can_register_individual_guest_with_dni()
    {
        $owner = User::factory()->create([
            'relationship_type' => 'owner',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($owner, 'owner');

        $lot = Lot::create([
            'number' => '9999',
            'code' => 'L9999',
            'balance' => 0.00,
        ]);
        $fu = $lot->functionalUnits()->create([
            'name' => 'UF 9999',
            'code' => 'UF9999',
            'balance' => 0.00,
        ]);
        $owner->functionalUnits()->attach($fu);

        $this->actingAs($owner);
        session(['active_lot_id' => $lot->id]);

        $response = $this->post(route('owner.guests.store'), [
            'type' => 'individual',
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '12345678',
            'license_plate' => 'AA123BB',
            'visit_date' => now()->toDateString(),
            'notes' => 'Familiar',
        ]);

        $response->assertRedirect(route('owner.guests.index'));
        $this->assertDatabaseHas('guest_authorizations', [
            'type' => 'individual',
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '12345678',
            'lot_id' => $lot->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_owner_cannot_register_individual_guest_without_dni()
    {
        $owner = User::factory()->create([
            'relationship_type' => 'owner',
            'first_login_at' => now(),
            'terms_accepted_at' => now(),
        ]);
        $this->assignRoleToUser($owner, 'owner');

        $lot = Lot::create([
            'number' => '9998',
            'code' => 'L9998',
            'balance' => 0.00,
        ]);
        $fu = $lot->functionalUnits()->create([
            'name' => 'UF 9998',
            'code' => 'UF9998',
            'balance' => 0.00,
        ]);
        $owner->functionalUnits()->attach($fu);

        $this->actingAs($owner);
        session(['active_lot_id' => $lot->id]);

        $response = $this->post(route('owner.guests.store'), [
            'type' => 'individual',
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'dni' => '', // Empty DNI should fail
            'visit_date' => now()->toDateString(),
        ]);

        $response->assertSessionHasErrors(['dni']);
    }
}
