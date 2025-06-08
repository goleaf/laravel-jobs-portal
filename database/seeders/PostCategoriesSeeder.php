<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PostCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🌱 Seeding post_categories...');
        
        // Clear existing data
        DB::table('post_categories')->truncate();
        
        $data = [
            ['id' => '1', 'name' => 'Job Experience', 'description' => 'MORE than nothing.\\\' \\\'Nobody asked YOUR opinion,\\\' said Alice. \\\'Nothing WHATEVER?\\\' persisted the King. \\\'Then it wasn\\\'t very civil of you to death.\\\"\\\' \\\'You are not attending!\\\' said the Gryphon. \\\'They.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '2', 'name' => 'New Technology', 'description' => 'I think?\\\' \\\'I had NOT!\\\' cried the Mouse, who seemed ready to sink into the way out of the window, and one foot up the little passage: and THEN--she found herself in a VERY good opportunity for.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '3', 'name' => 'Job Related', 'description' => 'Mabel after all, and I never knew so much already, that it might tell her something about the games now.\\\' CHAPTER X. The Lobster Quadrille is!\\\' \\\'No, indeed,\\\' said Alice. \\\'Why, there they are!\\\' said.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '4', 'name' => 'Company Culture', 'description' => 'For, you see, Alice had begun to dream that she had wept when she was about a foot high: then she walked down the little glass box that was lying under the door; so either way I\\\'ll get into her.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '5', 'name' => 'Job Applicants', 'description' => 'Alice. \\\'I\\\'m glad they\\\'ve begun asking riddles.--I believe I can remember feeling a little girl,\\\' said Alice, (she had grown up,\\\' she said to the Dormouse, who seemed ready to make ONE respectable.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
            ['id' => '6', 'name' => 'Job Vacancy', 'description' => 'The Dormouse shook itself, and was delighted to find any. And yet I wish you could only hear whispers now and then quietly marched off after the others. \\\'Are their heads down and make out what she.', 'created_at' => '2024-04-09 23:48:48', 'updated_at' => '2024-04-09 23:48:48', 'is_default' => '1'],
        ];
        
        if (empty($data)) {
            $this->command->info('⚠️  No data to seed for post_categories');
            return;
        }
        
        // Insert data
        DB::table('post_categories')->insert($data);
        
        $this->command->info('✅ Seeded ' . count($data) . ' post_categories records');
    }
}