<?php

namespace Database\Seeders;

use Database\Seeders\Organization\OrganizationWithOUsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Auth\SysadmiRolesAndPermissionsSeeder;
use Database\Seeders\Auth\UserSeeder;
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
            OrganizationWithOUsSeeder::class,
        ]);
    }
}
