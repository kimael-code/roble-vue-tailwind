<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');
        $locale = str_replace('-', '_', $locale);

        $supportedLocales = ['es_VE', 'en_US'];

        if (!in_array($locale, $supportedLocales))
        {
            // Fallback al lenguaje base (ej: es_VE -> es)
            $fallbackLocale = explode('_', $locale)[0];
            $locale = in_array($fallbackLocale, ['es', 'en']) ? $fallbackLocale : config('app.fallback_locale');
        }

        app()->setLocale($locale);

        URL::defaults(['locale' => $request->route('locale')]);

        return $next($request);
    }
}
