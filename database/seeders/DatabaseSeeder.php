<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Reservation;
use App\Models\Payment;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Buat akun admin default
        $admin = User::firstOrCreate([
            'email' => 'admin@booking.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password123'),
        ]);

        // Assign role
        $admin->assignRole($adminRole);

        User::factory(3)->create()->each(function ($user) {
        $properties = Property::factory(2)->create(['owner_id' => $user->id]);
        $properties->each(function ($property) {
            $units = Unit::factory(5)->create(['property_id' => $property->id]);
            $units->each(function ($unit) {
                $reservations = Reservation::factory(3)->create(['unit_id' => $unit->id]);
                $reservations->each(function ($reservation) {
                    Payment::factory()->create(['reservation_id' => $reservation->id]);
                });
            });
        });
    });
    }
}

