<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LangManager
{
    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(\Illuminate\Contracts\Auth\Factory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        # Check header request and determine localization
        $lang = $request->hasHeader('X-Localization') && !empty($request->header('X-Localization'))
            ? $request->header('X-Localization')
            : session('_locale', 'ru');

        # set laravel localization
        app()->setLocale($lang);

        # set translatable locale
        config(['translatable.locale' => $lang]);

        return $next($request);
    }
}
