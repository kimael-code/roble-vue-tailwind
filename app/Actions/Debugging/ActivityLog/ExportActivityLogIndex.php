<?php

namespace App\Actions\Debugging\ActivityLog;

use App\Models\Debugging\ActivityLog;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportActivityLogIndex
{
    public function handle(array $inputs): string
    {
        $data = ActivityLog::filter($inputs)->get();

        return Pdf::loadView('pdf.activity-logs.index', ['data' => $data])
            ->setPaper('a4', 'landscape')->output();
    }
}
