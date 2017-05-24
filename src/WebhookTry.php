<?php

namespace Jurihub\LaravelWebhooks;

use Illuminate\Database\Eloquent\Model;

class WebhookTry extends Model {
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function isSuccess()
    {
        return $this->response_code == 200;
    }
    
    /**
     * Get the webhook that owns the try.
     */
    public function webhook()
    {
        return $this->belongsTo(Webhook::class);
    }
}
