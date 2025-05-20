<?php

namespace App\Http\Controllers\Debugging;

use App\Actions\Debugging\DeleteLogFile;
use App\Actions\Debugging\ExportLogFile;
use App\Http\Controllers\Controller;
use App\Support\Props\Debugging\LogFileProps;
use Inertia\Inertia;

class LogFileController extends Controller
{
    public function index()
    {
        return Inertia::render('debugging/log-files/Index', LogFileProps::index());
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
