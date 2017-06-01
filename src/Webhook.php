<?php

namespace Jurihub\LaravelWebhooks;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model {
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'closed_at',
        'created_at', 'updated_at',
    ];
    
    public function cancel()
    {
        $this->close('cancel');
    }
    
    public function close($status)
    {
        $this->is_closed = true;
        $this->closed_at = Carbon::now();
        $this->status = $status;
        $this->save();
        
        $this->unlock();
    }
    
    public function data2send()
    {
        $data = json_decode($this->data, true);
        $data['user_id'] = $this->user_id;
        $data['type'] = $this->type;
        return $data;
    }
    
    public function handleError($responseCode, $response)
    {
        $this->tries()->create([
            'response_code' => $responseCode,
            'response_body' => $response,
        ]);
        
        $this->incrementTriesCounter();
        
        if ($this->nb_tries > self::maxTries()) {
            $this->close('failed');
        } else {
            $this->unlock();
        }
    }
    
    public function handleResponse($data)
    {
        $this->tries()->create([
            'response_code' => 200,
            'response_body' => $data,
        ]);
        
        $this->incrementTriesCounter();
        
        $this->close('success');
    }
    
    public function incrementTriesCounter()
    {
        $this->nb_tries = $this->nb_tries + 1;
        $this->last_tried_at = Carbon::now();
        $this->save();
    }
    
    public static function maxTries()
    {
        return config('webhooks.max-tries', 10);
    }
    
    /**
     * Get the tries for the webhook.
     */
    public function tries()
    {
        return $this->hasMany(WebhookTry::class);
    }
    
    public function send()
    {
        $this->is_working = true;
        $this->save();
        
        $http = new \GuzzleHttp\Client;
        try {
            $response = $http->post($this->target, [
                'form_params' => $this->data2send(),
            ]);
        } catch(\Exception $e) {
            $this->handleError($e->getCode(), trim($e->getMessage()));
            return;
        }
        
        $responseBody = json_decode((string) $response->getBody(), true);
        
        if ($response->getStatusCode() != 200) {
            $this->handleError($response->getStatusCode(), $responseBody);
            return;
        }

        $this->handleResponse($responseBody);
    }
    
    public function unlock()
    {
        $this->is_working = false;
        $this->save();
    }
}
