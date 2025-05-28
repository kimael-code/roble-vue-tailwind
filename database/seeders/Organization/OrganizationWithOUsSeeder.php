<?php

namespace Database\Seeders\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationalUnit;
use Illuminate\Database\Seeder;

class OrganizationWithOUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('local'))
        {
            // ente activo con sus unidades administrativas
            $activeOrganization = Organization::factory()->create();

            $rootOU = OrganizationalUnit::factory()
                ->for($activeOrganization)
                ->create(['organizational_unit_id' => null]);

            $firstLvlOus = OrganizationalUnit::factory()
                ->count(3)
                ->for($activeOrganization)
                ->create(['organizational_unit_id' => $rootOU->id]);

            foreach ($firstLvlOus as $ou)
            {
                OrganizationalUnit::factory()
                    ->count(3)
                    ->for($activeOrganization)
                    ->create(['organizational_unit_id' => $ou->id]);
            }

            // ente inactivo con sus unidades administrativas
            $inactiveOrganization = Organization::factory()->disabled()->create();

            $rootOU = OrganizationalUnit::factory()
                ->for($inactiveOrganization)
                ->create(['organizational_unit_id' => null]);

            $firstLvlOus = OrganizationalUnit::factory()
                ->count(3)
                ->for($inactiveOrganization)
                ->create(['organizational_unit_id' => $rootOU->id]);

            foreach ($firstLvlOus as $ou)
            {
                OrganizationalUnit::factory()
                    ->count(3)
                    ->for($inactiveOrganization)
                    ->create(['organizational_unit_id' => $ou->id]);
            }
        }
    }
}
