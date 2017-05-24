<?php

namespace Jurihub\LaravelWebhooks;

use Illuminate\Support\Facades\Facade;

class WebhooksFacade extends Facade
{
    protected static function getFacadeAccessor() { 
        return 'webhook';
    }
}