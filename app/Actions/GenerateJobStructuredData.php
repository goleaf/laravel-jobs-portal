<?php

namespace App\Actions;

use LumoSolutions\Actionable\Traits\IsDispatchable;
use LumoSolutions\Actionable\Traits\IsRunnable;

class GenerateJobStructuredData
{
    use IsDispatchable;
    use IsRunnable;

    public function handle(): void
    {
        // Action logic here
    }
}
