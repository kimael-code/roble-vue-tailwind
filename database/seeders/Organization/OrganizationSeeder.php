<?php

namespace Database\Seeders\Organization;

use App\Models\Organization\Organization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local'))
        {
            Organization::factory()
                ->count(1)
                ->create();
        }
    }
}
