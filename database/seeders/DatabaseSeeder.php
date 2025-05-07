<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\SysadmiRolesAndPermissionsSeeder;
use Database\Seeders\Auth\UserSeeder;
use Database\Seeders\Organization\OrganizationalUnitSeeder;
use Database\Seeders\Organization\OrganizationSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SysadmiRolesAndPermissionsSeeder::class,
            UserSeeder::class,
            OrganizationSeeder::class,
            OrganizationalUnitSeeder::class,
        ]);
    }
}
