<?php

namespace App\Helpers;

use App\Webhook;
use App\Jobs\DispatchWebhook;

class WebhookHelper
{
    /**
     * Dispatch all active webhooks for a given event.
     *
     * @param string $event
     * @param array $data
     * @return void
     */
    public static function dispatch($event, $data)
    {
        $webhooks = Webhook::where('event', $event)
            ->where('status', 'active')
            ->get();

        foreach ($webhooks as $webhook) {
            DispatchWebhook::dispatch($webhook, $data);
        }
    }
}
