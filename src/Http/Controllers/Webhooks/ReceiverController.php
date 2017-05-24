<?php

namespace Jurihub\LaravelWebhooks\Http\Controllers\Webhooks;

use Log;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

class ReceiverController extends Controller
{
    /**
     * Handle a Stripe webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        //$payload = json_decode($request->getContent(), true);
        $payload = $request->all();

        $method = 'handle'.studly_case(str_replace('.', '_', $payload['type']));

        if (method_exists($this, $method)) {
            return $this->{$method}($payload);
        } else {
            return $this->missingMethod($payload);
        }
    }

    /**
     * Verify if we are in the testing environment.
     *
     * @return bool
     */
    protected function isInTestingEnvironment()
    {
        return env('APP_ENV') !== 'prod';
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array   $parameters
     * @return mixed
     */
    public function missingMethod($parameters = [])
    {
        return new Response;
    }
    
}
