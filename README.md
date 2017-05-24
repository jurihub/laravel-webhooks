# laravel-webhooks
Laravel package to handle Webhooks

## Installation guide

Include the page via Composer:

    composer require jurihub/laravel-webhooks

Add the Webhooks service provider to your `config/app.php` file in the `providers` array:

`Jurihub\LaravelWebhooks\WebhooksServiceProvider::class`

To use a Facade instead of injecting the class itself, add this to the `aliases` array in the same file:

`'Webhooks' => Jurihub\LaravelWebhooks\WebhooksFacade::class`

Publish the configuration file:

`php artisan vendor:publish --provider="Jurihub\LaravelWebhooks\WebhooksServiceProvider" --tag="config"`

You certainly will want to add some endpoints, list them in the `targets` array in `config/webhooks.php`

Launch the migrations (provided automatically by the ServiceProvider), that will create 2 new tables: `webhooks` and `webhook_tries`

`php artisan migrate`

The first attempt is sent automatically, but if you want to automatize retries, add the following schedule in your `app/Console/Kernel.php` file:

    $schedule->call(function () {
        \Jurihub\LaravelWebhooks\Http\Controllers\Webhooks\SenderController::retry();
    })->everyMinute();

To handle incoming webhooks, create a new controller, eg. `App\Http\Controllers\Webhooks\ReceiverController.php`

    namespace App\Http\Controllers\Webhooks;
    
    use Symfony\Component\HttpFoundation\Response;
    use Jurihub\LaravelWebhooks\Http\Controllers\LaravelWebhooks\ReceiverController as WebhooksReceiverController;
    
    class ReceiverController extends WebhooksReceiverController
    {
        public function handleUserUpdated($data)
        {
            // handling $data here
        }
    }

And add the route to your `routes/api.php` file to receive the incoming webhooks.
You may want to customize the endpoint, according to the `targets` listed in the `config/app.php` file.

`Route::post('webhook', 'Webhooks\ReceiverController@handleWebhook');`

Activate the webhooks' sending queue:

`php artisan queue:work database --queue=webhook --tries=3`