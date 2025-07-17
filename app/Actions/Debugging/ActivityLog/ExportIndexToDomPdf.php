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
    public function __construct(
        private array $filters = [],
        protected string $orientation = 'L',
        protected string $format = 'LETTER',
    ) {}

    public static function make(): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('pdf.activity-logs.index')
            ->setPaper('letter', 'landscape');
    }
}
