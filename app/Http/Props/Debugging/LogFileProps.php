<?php

namespace App\Http\Props\Debugging;

use App\Support\Logs\Logfile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogFileProps
{
    private static function getPermissions(): array
    {
        return [
            'create' => false,
            'read' => Auth::user()->can('read system log') || Auth::user()->hasRole(__('Superuser')),
            'update' => false,
            'delete' => Auth::user()->can('delete system logs') || Auth::user()->hasRole(__('Superuser')),
            'export' => Auth::user()->can('export system logs') || Auth::user()->hasRole(__('Superuser')),
        ];
    }

    public static function index(): array
    {
        $logs = new Logfile();

        return [
            'can' => self::getPermissions(),
            'filters' => Request::all(['search', 'file']),
            'logFiles' => array_keys($logs->relativePaths()),
            'logs' => $logs->logs(Request::input('file', 'laravel.log')),
        ];
    }
}
