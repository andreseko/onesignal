<?php


namespace AndreSeko\OneSignal;


use Illuminate\Support\ServiceProvider;

/**
 * Class OneSignalServiceProvider
 * @author Andre Goncalves <andreseko@gmail.com>
 * @version 1.0.0
 * @package AndreSeko\OneSignal
 */
class OneSignalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/onesignal.php';

        if ($this->app->runningInConsole()) {
            $this->publishes([$configPath => config_path('onesignal.php')], 'config');
        }

        $this->mergeConfigFrom($configPath, 'onesignal');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(OneSignal::class, function ($app) {
            $config = isset($app['config']['services']['onesignal']) ? $app['config']['services']['onesignal'] : null;
            if (is_null($config)) {
                $config = $app['config']['onesignal'] ?: $app['config']['onesignal::config'];
            }

            return new OneSignal($config['app_id'], $config['rest_api_key']);
        });

        $this->app->alias('onesignal', OneSignal::class);
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ["onesignal", OneSignal::class];
    }
}