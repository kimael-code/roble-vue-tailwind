<?php

namespace App\Actions\Debugging\ActivityLog;

use App\Models\Debugging\ActivityLog;

class ExportActivityLogIndex
{
    public function handle(array $inputs): string
    {
        $data = ActivityLog::filter($inputs)->get();

        

        // $pdf

        // return Pdf::loadView('pdf.activity-logs.index', ['data' => $data])
        //     ->setPaper('a4', 'landscape')->output();
    }
}
