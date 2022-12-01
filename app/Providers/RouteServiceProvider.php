<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * This namespace is applied to the controller routes in your web routes file.
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $webNamespace = 'App\\Http\\Controllers\\Web';

    /**
     * This namespace is applied to the controller routes in your api routes file.
     *
     * @var string
     */
    protected $apiNamespace = 'App\\Http\\Controllers\\Api';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->mapWebRoutes();

        $this->mapApiRoutes();
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }

    /**
     * Configure a web routes for the application.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->webNamespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Configure an api routes for the application.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        $fileSystem = new \Illuminate\Filesystem\Filesystem();
        $files = $fileSystem->files(base_path('routes/api'));

        if (! empty($files) && is_array($files)) {
            foreach ($files as $file) {
                Route::prefix('api/v1')
                    ->middleware('api')
                    ->namespace($this->apiNamespace)
                    ->group($file->getRealPath());
            }
        }
    }
}
