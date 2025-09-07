<?php

namespace Database\Seeders\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationalUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationWithOUsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()->environment('local') ? $this->localSeed() : $this->nonLocalSeed();
    }

    private function localSeed(): void
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

    private function nonLocalSeed(): void
    {
        $organizationID = DB::table('organizations')->insertGetId([
            'rif' => 'X-99999999-9',
            'name' => 'Claddagh Software Solutions Ltd.',
            'acronym' => 'LAMPRE',
            'address' => 'Unit 4B, The Grange Business Park. Dublin 12. D12 X3F8 - Ireland',
            'created_at' => 'now()',
            'updated_at' => 'now()',
        ]);

        $query = "INSERT INTO
             organizational_units (
                 organization_id,
                 organizational_unit_id,
                 code,
                 name,
                 acronym,
                 floor,
                 created_at,
                 updated_at,
                 disabled_at
             )
             VALUES
             ($organizationID, NULL, NULL, 'BOARD OF DIRECTORS', 'BOD', 'HF', 'now()', 'now()', NULL)";
        DB::unprepared($query);
    }
}
