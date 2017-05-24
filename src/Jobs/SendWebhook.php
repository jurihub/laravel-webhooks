<?php

namespace Jurihub\LaravelWebhooks\Jobs;

use Illuminate\Bus\Queueable;
use Jurihub\LaravelWebhooks\Webhook;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWebhook implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $type;
    protected $userId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId, $type, $data)
    {
        $this->onQueue('webhook');
        $this->userId = $userId;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach (config('webhooks.targets') as $target) {
            $webhook = Webhook::create([
                'user_id' => $this->userId,
                'type' => $this->type,
                'target' => $target,
                'data' => json_encode($this->data),
            ]);
            $webhook->send();
        }
    }
}
