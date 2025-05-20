<?php

namespace App\Support\Logs;

class Level
{
    /**
     * Mapeo de niveles de mensajes a clases CSS.
     * @var <string, string>
     */
    private static $levels = [
        'debug' => 'info',
        'info' => 'info',
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'critical' => 'danger',
        'alert' => 'danger',
        'emergency' => 'danger',
        'processed' => 'info',
        'failed' => 'warning',
    ];

    /**
     * Mapeo de niveles de mensajes a íconos web.
     * @var array<string, string>
     */
    private static $icons = [
        'debug' => 'circle-info',
        'info' => 'circle-info',
        'notice' => 'circle-info',
        'warning' => 'triangle-exclamation',
        'error' => 'triangle-exclamation',
        'critical' => 'skull-crossbones',
        'alert' => 'bell',
        'emergency' => 'truck-medical',
        'processed' => 'circle-info',
        'failed' => 'circle-exclamation'
    ];

    /**
     * Devuelve todos los nombres de niveles.
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_keys(self::$levels);
    }

    /**
     * Devuelve el ícono para el `$level` especificado.
     * @param string $level
     * @return string
     */
    public static function icon(string $level): string
    {
        return self::$icons[$level];
    }

    /**
     * Devuelve la clase CSS para el `$level` especificado.
     * @param string $level
     * @return string
     */
    public static function cssClass(string $level): string
    {
        return self::$levels[$level];
    }
}
