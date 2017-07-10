<?php

namespace Jurihub\LaravelWebhooks\Http\Controllers\Webhooks;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jurihub\LaravelWebhooks\Webhook;
use Symfony\Component\HttpFoundation\Response;

class SenderController extends Controller
{
    /**
     * Verify if we are in the testing environment.
     *
     * @return bool
     */
    public static function isInTestingEnvironment()
    {
        return !in_array(env('APP_ENV'),['prod','production']);
    }
    
    public static function retry()
    {
        $webhook = Webhook
            ::where([
                ['is_working', '=', 0],
                ['is_closed', '=', 0],
                ['last_tried_at', '<', Carbon::now()->subMinutes(self::isInTestingEnvironment() ? 1 : 30)],
            ])
            ->whereNotNull('last_tried_at')
            ->orderBy('id', 'desc')
            ->first();
        if ($webhook !== null) {
            $webhook->send();
        }
    }
}
