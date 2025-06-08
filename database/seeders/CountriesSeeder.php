<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding countries...');
        
        // Clear existing data
        DB::table('countries')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Afghanistan', 'short_code' => 'AF', 'phone_code' => '93', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '2', 'name' => 'Albania', 'short_code' => 'AL', 'phone_code' => '355', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '3', 'name' => 'Algeria', 'short_code' => 'DZ', 'phone_code' => '213', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '4', 'name' => 'American Samoa', 'short_code' => 'AS', 'phone_code' => '1684', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '5', 'name' => 'Andorra', 'short_code' => 'AD', 'phone_code' => '376', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '6', 'name' => 'Angola', 'short_code' => 'AO', 'phone_code' => '244', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '7', 'name' => 'Anguilla', 'short_code' => 'AI', 'phone_code' => '1264', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '8', 'name' => 'Antarctica', 'short_code' => 'AQ', 'phone_code' => '0', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '9', 'name' => 'Antigua And Barbuda', 'short_code' => 'AG', 'phone_code' => '1268', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['id' => '10', 'name' => 'Argentina', 'short_code' => 'AR', 'phone_code' => '54', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for countries');
            return;
        }
        
        // Insert data
        DB::table('countries')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' countries records');
    }
}