<?php

namespace Database\Seeders;

use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Job Categories Taxonomy
        $jobCategoriesTaxonomy = Taxonomy::create([
            'name' => 'Job Categories',
            'slug' => 'job_category',
            'description' => 'Primary job categories for organizing job listings',
            'type' => 'job_category',
            'is_hierarchical' => true,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 1,
        ]);

        // Create basic job category terms
        $jobCategories = [
            'Technology',
            'Healthcare',
            'Finance',
            'Education',
            'Marketing',
            'Engineering',
            'Design',
            'Sales',
        ];

        foreach ($jobCategories as $index => $category) {
            Term::create([
                'taxonomy_id' => $jobCategoriesTaxonomy->id,
                'name' => $category,
                'slug' => strtolower(str_replace(' ', '-', $category)),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Skills Taxonomy
        $skillsTaxonomy = Taxonomy::create([
            'name' => 'Skills',
            'slug' => 'skill',
            'description' => 'Technical and soft skills',
            'type' => 'skill',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 2,
        ]);

        // Create basic skill terms
        $skills = [
            'JavaScript',
            'Python',
            'PHP',
            'React',
            'Laravel',
            'MySQL',
            'Communication',
            'Leadership',
        ];

        foreach ($skills as $index => $skill) {
            Term::create([
                'taxonomy_id' => $skillsTaxonomy->id,
                'name' => $skill,
                'slug' => strtolower(str_replace(' ', '-', $skill)),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Job Types Taxonomy
        $jobTypesTaxonomy = Taxonomy::create([
            'name' => 'Job Types',
            'slug' => 'job_type',
            'description' => 'Employment types',
            'type' => 'job_type',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 3,
        ]);

        // Create basic job type terms
        $jobTypes = [
            'Full-time',
            'Part-time',
            'Contract',
            'Remote',
            'Freelance',
        ];

        foreach ($jobTypes as $index => $type) {
            Term::create([
                'taxonomy_id' => $jobTypesTaxonomy->id,
                'name' => $type,
                'slug' => strtolower(str_replace(' ', '-', $type)),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        $this->command->info('Basic taxonomy system seeded successfully!');
        $this->command->info('Created '.Taxonomy::count().' taxonomies');
        $this->command->info('Created '.Term::count().' terms');
    }
}
