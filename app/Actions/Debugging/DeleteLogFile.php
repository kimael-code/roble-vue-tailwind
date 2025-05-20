<?php

namespace App\Actions\Debugging;

use App\Support\Logs\Logfile;
use Illuminate\Support\Facades\Storage;

class DeleteLogFile
{
    public static function handle(string $file): void
    {
        $logfile = new Logfile();

        $fileBeingDeleted = '';

        if (array_key_exists($file, $logfile->relativePaths()))
        {
            $fileBeingDeleted = $logfile->relativePaths()[$file];
        }

        Storage::disk('logs')->delete($fileBeingDeleted);

        session()->flash('message', [
            'message' => $file,
            'title' => __('DELETED!'),
            'type'  => 'danger',
        ]);
    }
}
