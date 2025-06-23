<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Placeholder for Translation Command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Translation command placeholder executed.');
    }
}
