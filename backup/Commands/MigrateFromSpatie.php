<?php

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateFromSpatie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:from-spatie';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from Spatie Media Library to our new files system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!Schema::hasTable('media')) {
            $this->error('Media table does not exist. Nothing to migrate.');

            return 1;
        }

        if (!Schema::hasTable('files')) {
            $this->error('Files table does not exist. Please run migrations first.');

            return 1;
        }

        $mediaCount = DB::table('media')->count();

        if (0 === $mediaCount) {
            $this->info('No media records found to migrate.');

            return 0;
        }

        $this->info("Found {$mediaCount} media records to migrate.");

        $bar = $this->output->createProgressBar($mediaCount);
        $bar->start();

        DB::table('media')->orderBy('id')->chunk(100, function ($medias) use ($bar) {
            foreach ($medias as $media) {
                // Check if file has already been migrated (to avoid duplicates)
                $exists = File::where('model_type', $media->model_type)
                    ->where('model_id', $media->model_id)
                    ->where('path', $media->id.'/'.$media->file_name)
                    ->exists()
                ;

                if (!$exists) {
                    // Create new file record
                    File::create([
                        'model_type' => $media->model_type,
                        'model_id' => $media->model_id,
                        'collection_name' => $media->collection_name,
                        'name' => $media->name,
                        'file_name' => $media->file_name,
                        'mime_type' => $media->mime_type,
                        'disk' => $media->disk,
                        'path' => $media->id.'/'.$media->file_name, // Use the same path structure as Spatie
                        'size' => $media->size,
                        'order_column' => $media->order_column,
                        'custom_properties' => json_encode($media->custom_properties ?? []),
                        'responsive_images' => json_encode($media->responsive_images ?? []),
                        'created_at' => $media->created_at,
                        'updated_at' => $media->updated_at,
                    ]);
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info('Migration from Spatie Media Library completed successfully.');

        return 0;
    }
}
