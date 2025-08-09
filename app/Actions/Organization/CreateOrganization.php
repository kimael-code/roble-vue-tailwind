<?php

namespace App\Actions\Organization;

use App\Models\Organization\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateOrganization
{
    public static function handle(array $inputs): void
    {
        DB::transaction(function () use ($inputs)
        {
            foreach (Organization::all() as $currentOrganization)
            {
                $currentOrganization->disabled_at = now();
                $currentOrganization->save();
            }

            $filePath = Storage::disk('public')->putFile('logos', $inputs['logo_path']);

            $newOrganization = new Organization($inputs);
            $newOrganization->logo_path = $filePath;
            $newOrganization->save();
        });
    }
}
