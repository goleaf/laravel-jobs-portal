<?php

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

/**
 * Class RemoveProviderUniqueRuleFromSocialAccountsSeeder.
 */
class RemoveProviderUniqueRuleFromSocialAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if table exists first
        if (! Schema::hasTable('social_accounts')) {
            return;
        }

        Schema::table('social_accounts', function (Blueprint $table) {
            // Skip for SQLite in testing as it doesn't support advanced schema introspection
            if (config('database.default') === 'sqlite') {
                return;
            }

            try {
                // Try to drop the unique constraint if it exists
                $table->dropUnique(['provider']);
            } catch (\Exception $e) {
                // If the constraint doesn't exist, continue silently
            }
        });
    }
}
