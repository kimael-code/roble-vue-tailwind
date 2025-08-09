<?php

namespace App\Http\Controllers\Exporters;

use App\Actions\Security\ExportRolesToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleExporterController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export roles'))
        {
            abort(403);
        }

        return new ExportRolesToPdf(filters: $request->all())->make();
    }
}
