<?php

namespace App\Actions\Organization;

use App\Models\Organization\OrganizationalUnit;
use Illuminate\Support\Facades\DB;

class UpdateOrganizationalUnit
{
    public static function handle(array $inputs, OrganizationalUnit $organizationalUnit): void
    {
        DB::transaction(function () use ($organizationalUnit, $inputs)
        {
            $organizationalUnit->code = $inputs['code'];
            $organizationalUnit->name = $inputs['name'];
            $organizationalUnit->acronym = $inputs['acronym'];
            $organizationalUnit->floor = $inputs['floor'];
            $organizationalUnit->organizationalUnit()->associate($inputs['organizational_unit_id']);
            $organizationalUnit->save();
        });
    }
}
