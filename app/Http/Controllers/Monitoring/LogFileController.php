<?php

namespace App\Http\Controllers\Monitoring;

use App\Actions\Monitoring\DeleteLogFile;
use App\Actions\Monitoring\ExportLogFile;
use App\Http\Controllers\Controller;
use App\Http\Props\Monitoring\LogFileProps;
use Inertia\Inertia;

class LogFileController extends Controller
{
    public function index()
    {
        return Inertia::render('monitoring/log-files/Index', LogFileProps::index());
    }

    public function export(string $file)
    {
        return ExportLogFile::handle($file);
    }

    public function delete(string $file)
    {
        DeleteLogFile::handle($file);

        return redirect()->route('log-files.index');
    }
}
