<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only drop the tables if they exist
        if (Schema::hasTable('media')) {
            Schema::dropIfExists('media');
        }
        
        // Drop any other tables that might be related to the media library
        if (Schema::hasTable('media_collections')) {
            Schema::dropIfExists('media_collections');
        }
        
        if (Schema::hasTable('mediable')) {
            Schema::dropIfExists('mediable');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // We can't recreate the tables without the package
        // The data would be lost anyway, so this migration can't be reversed
    }
}; 