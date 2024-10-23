<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role3 = Role::create(['name' => 'Super Admin']);
        $user = User::factory()->create([
            'name' => 'Super Admin',
            'email_address' => 'superadmin@example.com',
            'username' => 'sa',
            'password' => Hash::make('SA'),
            'phone_number' => '+60182901980',
            'gender' => 'M'
        ])->assignRole($role3);

        $address = new Address([
            'line_1' => $faker->streetAddress(),
            'line_2' => $faker->secondaryAddress(),
            'postcode' => $faker->postcode(),
            'city' => $faker->city(),
            'state' => $faker->state(),
        ]);
        $user->address()->save($address);

        $address->country()->create([
            'iso2' => strtolower($faker->countryCode()),
            'name' => $faker->country()
        ]);
    }
}
