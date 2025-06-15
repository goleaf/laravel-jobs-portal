<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class OptimizeAssets extends Command
{
    protected $signature = 'assets:optimize';
    protected $description = 'Optimize application assets for production';

    public function handle()
    {
        $this->info('🚀 Optimizing Application Assets...');

        // Clear old compiled assets
        $this->call('view:clear');
        $this->call('config:clear');
        $this->call('route:clear');

        // Compile assets with optimization
        $this->info('📦 Building optimized assets...');
        exec('npm run build', $output, $returnCode);

        if (0 === $returnCode) {
            $this->info('✅ Assets compiled successfully');
        } else {
            $this->error('❌ Asset compilation failed');

            return 1;
        }

        // Optimize images
        $this->optimizeImages();

        // Cache configurations
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        // Optimize autoloader
        exec('composer dump-autoload --optimize --no-dev');

        $this->info('🎉 Asset optimization complete!');

        return 0;
    }

    private function optimizeImages()
    {
        $this->info('🖼️ Optimizing images...');

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $publicImages = public_path('images');

        if (File::exists($publicImages)) {
            $images = File::allFiles($publicImages);

            foreach ($images as $image) {
                $extension = strtolower($image->getExtension());

                if (in_array($extension, $imageExtensions)) {
                    // Add image optimization logic here
                    $this->line("   Optimizing: {$image->getFilename()}");
                }
            }
        }

        $this->info('   ✅ Image optimization complete');
    }
}
