<?php
namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Laravel\Paddle\Events\WebhookReceived;

class PaddleEventListener {
    /**
     * Handle received Paddle webhooks.
     */
    public function handle(WebhookReceived $event): void {
        if ($event->payload['event_type'] === 'subscription.activated') {
            Log::info('Paddle subscription activated', $event->payload);
        }
    }
}
