<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class AdminNotification
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(string $type, array $payload = []): void
    {
        // Placeholder for admin notification system integration
        Log::info('Admin notification', [
            'type' => $type,
            'payload' => $payload,
        ]);
    }
}
