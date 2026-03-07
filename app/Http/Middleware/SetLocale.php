<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        if (session()->has('locale')) {
            $locale = session()->get('locale');
        }
        elseif (auth()->check()) {
            $locale = auth()->user()->language;
            if ($locale) {
                session()->put('locale', $locale);
            }
        }

        if ($locale) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
