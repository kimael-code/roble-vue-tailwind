<?php

namespace App\Actions\Organization;

use App\Models\Organization\Organization;
use Illuminate\Support\Facades\Storage;

class DeleteOrganization
{
    public static function handle(Organization $organization): void
    {
        $logoPath = $organization->logo_path;

        $organization->delete();

        if ($logoPath)
        {
            Storage::disk('public')->delete($organization->logo_path);
        }
    }
}
