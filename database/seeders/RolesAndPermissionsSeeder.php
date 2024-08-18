<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create roles
        $superAdmin = Role::create(['name' => 'superAdmin']);
        $admin = Role::create(['name' => 'admin']);

        $permissions = [
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
        ];

        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions([
            'subscriptionProcess',
            'tagProcess',
            'articleProcess',
            'articleCategoryProcess',
            'memberProcess',
            'paymentProcess',
        ]);
    }
}