<?php

namespace App\Actions\Monitoring;

use App\Support\Logs\Logfile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportLogFile
{
    public static function handle(string $file): BinaryFileResponse
    {
        $logfile = new Logfile();

        $fileBeingDownloaded = '';

        if (array_key_exists($file, $logfile->relativePaths()))
        {
            $fileBeingDownloaded = $logfile->relativePaths()[$file];
        }

        return response()->download(Storage::disk('logs')->path($fileBeingDownloaded));
    }
}
