<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the superAdmin user
        $user = User::create([
            'name' => 'superAdmin',
            'email' => 'superAdmin@gmail.com',
            'password' => Hash::make('123456789'),
        ]);

        // Assign the "superAdmin" role using the sanctum guard
        $role = Role::where('name', 'superAdmin')->first();
        $user->assignRole($role);

        // Retrieve permissions with the sanctum guard
        $permissions = Permission::whereIn('name', [
            'userProcess',
            'sportProcess',
            'roomProcess',
            'facilityProcess',
            'mediaProcess',
            'subscriptionProcess',
            'tagProcess',
            'articleCategoryProcess',
            'articleProcess',
            'offerProcess',
            'memberProcess',
            'paymentProcess',
        ])->get();

        // Assign permissions using the sanctum guard
        $user->givePermissionTo($permissions);
    }
}