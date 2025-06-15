<?php

namespace App\Actions;

use LumoSolutions\Actionable\Traits\IsRunnable;
use LumoSolutions\Actionable\Traits\IsDispatchable;

class AnalyzeJobApplicationMatch
{
    use IsRunnable, IsDispatchable;

    public function handle(): void
    {
        // Action logic here
    }
}
