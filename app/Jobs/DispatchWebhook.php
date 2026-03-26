<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class DispatchWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $webhook;
    protected $data;

    public function __construct($webhook, $data)
    {
        $this->webhook = $webhook;
        $this->data = $data;
    }

    public function handle()
    {
        $client = new Client();
        
        $payload = [
            'event' => $this->webhook->event,
            'data' => $this->data,
            'timestamp' => now()->toISOString()
        ];

        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'MedicalPlace-Webhook/1.0'
        ];

        if ($this->webhook->secret) {
            $headers['X-Webhook-Signature'] = hash_hmac('sha256', json_encode($payload), $this->webhook->secret);
        }

        try {
            $response = $client->post($this->webhook->url, [
                'json' => $payload,
                'headers' => $headers,
                'timeout' => 10
            ]);

            Log::info("Webhook sent to " . $this->webhook->url . " Status: " . $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error("Webhook failed to " . $this->webhook->url . " Error: " . $e->getMessage());
        }
    }
}
