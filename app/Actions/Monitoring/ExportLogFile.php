<?php

namespace App\Actions\Monitoring;

use App\Support\Logs\Logfile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportLogFile
{
    public static function handle(string $file): StreamedResponse
    {
        $logfile = new Logfile();

        $fileBeingDownloaded = '';

        if (array_key_exists($file, $logfile->relativePaths()))
        {
            $fileBeingDownloaded = $logfile->relativePaths()[$file];
        }

        return Storage::disk('logs')->download($fileBeingDownloaded);
    }
}
