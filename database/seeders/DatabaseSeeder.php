<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Barber;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@booking.com',
            'password' => hash::make('12345678'),
        ]);

        //data barber
        $barbers = [
            ['name' => 'Barber Abi', 'is_active' => true],
            ['name' => 'Barber surya', 'is_active' => true],
            ['name' => 'Barber sikumbang', 'is_active' => false],
        ];
        foreach ($barbers as $barber) {
            Barber::create($barber);
        }
    }
}

