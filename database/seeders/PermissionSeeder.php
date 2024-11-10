<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Role;
use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
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

        $user = User::factory()->create([
            'name' => 'Super Admin',
            'username' => 'sa',
            'password' => Hash::make('SA'),
            'gender' => 'M'
        ]);

        $user_permissions = [
            Permission::create(['name' => 'users']),
            Permission::create(['name' => 'users.view']),
            Permission::create(['name' => 'users.create']),
            Permission::create(['name' => 'users.update']),
            Permission::create(['name' => 'users.delete']),

            Permission::create(['name' => 'roles']),
            Permission::create(['name' => 'roles.view']),
            Permission::create(['name' => 'roles.create']),
            Permission::create(['name' => 'roles.update']),
            Permission::create(['name' => 'roles.delete']),

            Permission::create(['name' => 'permissions']),
            Permission::create(['name' => 'permissions.view']),
        ];

        Role::create(["name" => 'Default']);

        $sa_role = Role::create(['name' => 'Super Admin']);
        $sa_role->syncPermissions($user_permissions);

        $user->assignRole($sa_role);

        $user->phone_number()->create([
            'iso2' => 'ms',
            'number' => '+60182901980'
        ]);

        $address = new Address([
            'line_1' => $faker->streetAddress(),
            'line_2' => $faker->secondaryAddress(),
            'postcode' => $faker->postcode(),
            'city' => $faker->city(),
            'state' => $faker->state(),
            'country' => $faker->country(),
        ]);
        $user->address()->save($address);
    }
}
