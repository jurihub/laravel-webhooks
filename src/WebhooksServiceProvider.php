<?php

namespace Jurihub\LaravelWebhooks;

use Illuminate\Support\ServiceProvider;

class WebhooksServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/webhooks.php' => config_path('webhooks.php'),
        ], 'config');
        
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
    
    public function register()
    {
        // facade
        $this->app->bind('webhook', function() {
            return new Webhook;
        });
    }
}