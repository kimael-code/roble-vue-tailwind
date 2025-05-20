<?php

namespace App\Support\Logs;

class Pattern
{
    /**
     * Expresiones regulares.
     * @var array<string, string>
     */
    private static $patterns = [
        'logs'        => '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?\].*/',
        'current_log' => [
            '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}([\+-]\d{4})?)\](?:.*?(\w+)\.|.*?)',
            ': (.*?)( in .*?:[0-9]+)?$/i'
        ],
        'files'       => '/\{.*?\,.*?\}/i',
    ];

    /**
     * Obtiene todos los nombres de los patrones definidos.
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_keys(self::$patterns);
    }

    /**
     * Devuelve la expresión regular dada por `$pattern`.
     * @param string $pattern
     * @param int|null $position
     * @return string
     */
    public static function get(string $pattern, int $position = null): string
    {
        return $position !== null
            ? self::$patterns[$pattern][$position]
            : self::$patterns[$pattern];
    }
}
