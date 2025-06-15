<?php

namespace App\Actions\Organization;

use App\Models\Organization\Organization;
use Illuminate\Support\Facades\Storage;

class UpdateOrganization
{
    public static function handle(array $inputs, Organization $organization): void
    {
        $oldLogo = $organization->logo_path;
        $logoHasChanged = false;

        if (gettype($inputs['logo_path']) === 'object')
        {
            $newFilePath = Storage::disk('public')->putFile('logos', $inputs['logo_path']);
        }

        try
        {
            $organization->rif = $inputs['rif'] ?? $organization->rif;
            $organization->name = $inputs['name'] ?? $organization->name;
            $organization->logo_path = $newFilePath ?? null ? $newFilePath : $inputs['logo_path'];
            $organization->acronym = $inputs['acronym'] ?? $organization->acronym;
            $organization->address = $inputs['address'] ?? $organization->address;
            $organization->disabled_at = $inputs['disabled'] ? now()->toIso8601String() : null;

            $logoHasChanged = $organization->isDirty('logo_path');

            $organization->save();

            if ($logoHasChanged && $oldLogo)
            {
                Storage::disk('public')->delete($oldLogo);
            }

        }
        catch (\Throwable $th)
        {
            if ($newFilePath)
            {
                Storage::disk('public')->delete($newFilePath);
            }
            throw $th;
        }
    }
}
