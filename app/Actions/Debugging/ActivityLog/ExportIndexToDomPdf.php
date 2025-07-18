<?php

namespace App\Actions\Debugging\ActivityLog;

use App\Models\Debugging\ActivityLog;
use App\Models\Organization\Organization;
use App\Support\DataExport\BasePdf;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ExportIndexToDomPdf
{
    public static function make(array $filters, string $orientation = 'L', string $format = 'LETTER'): \Barryvdh\DomPDF\PDF
    {
        // dd($filters);
        return Pdf::loadView('pdf.activity-logs.index')
            ->setPaper('letter', 'landscape');
    }
}
