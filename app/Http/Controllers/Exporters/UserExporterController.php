<?php

namespace App\Http\Controllers\Exporters;

use App\Actions\Security\ExportUsersToPdf;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserExporterController extends Controller
{
    public function indexToPdf(Request $request): string
    {
        if ($request->user()->cannot('export users'))
        {
            abort(403);
        }

        return new ExportUsersToPdf(filters: $request->all())->make();
    }
}
