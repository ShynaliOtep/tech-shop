<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LocaleMiddleware
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = Session::get('locale');
        if ($locale) {
            App::setLocale($locale);
        } else {
            App::setLocale('ru');
            session()->put('locale', 'ru');
        }

        return $next($request);
    }
}
