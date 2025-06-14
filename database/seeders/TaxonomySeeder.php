<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Taxonomy;
use App\Models\Term;

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

        $jobCategories = [
            'Technology' => [
                'Software Development',
                'Web Development',
                'Mobile Development',
                'DevOps & Cloud',
                'Data Science',
                'Machine Learning',
                'Cybersecurity',
                'System Administration',
                'Quality Assurance',
                'Technical Support',
            ],
            'Healthcare' => [
                'Nursing',
                'Medical Practice',
                'Healthcare Administration',
                'Pharmacy',
                'Medical Research',
                'Mental Health',
                'Physical Therapy',
                'Medical Technology',
            ],
            'Finance & Banking' => [
                'Investment Banking',
                'Corporate Finance',
                'Accounting',
                'Financial Planning',
                'Risk Management',
                'Insurance',
                'Auditing',
                'Credit Analysis',
            ],
            'Education' => [
                'Primary Education',
                'Secondary Education',
                'Higher Education',
                'Online Education',
                'Training & Development',
                'Educational Administration',
                'Curriculum Development',
            ],
            'Marketing & Sales' => [
                'Digital Marketing',
                'Content Marketing',
                'Sales Management',
                'Business Development',
                'Social Media Marketing',
                'SEO/SEM',
                'Brand Management',
                'Market Research',
            ],
            'Human Resources' => [
                'Talent Acquisition',
                'HR Management',
                'Training & Development',
                'Compensation & Benefits',
                'Employee Relations',
                'HR Analytics',
            ],
            'Engineering' => [
                'Civil Engineering',
                'Mechanical Engineering',
                'Electrical Engineering',
                'Chemical Engineering',
                'Environmental Engineering',
                'Industrial Engineering',
            ],
            'Design & Creative' => [
                'Graphic Design',
                'UI/UX Design',
                'Web Design',
                'Product Design',
                'Architecture',
                'Interior Design',
                'Photography',
                'Video Production',
            ],
        ];

        foreach ($jobCategories as $parentCategory => $subCategories) {
            $parent = Term::create([
                'taxonomy_id' => $jobCategoriesTaxonomy->id,
                'name' => $parentCategory,
                'slug' => \Str::slug($parentCategory),
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 0,
                'level' => 0,
            ]);

            foreach ($subCategories as $index => $subCategory) {
                Term::create([
                    'taxonomy_id' => $jobCategoriesTaxonomy->id,
                    'name' => $subCategory,
                    'slug' => \Str::slug($subCategory),
                    'parent_id' => $parent->id,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'level' => 1,
                    'path' => $parentCategory,
                ]);
            }
        }

        // Skills Taxonomy
        $skillsTaxonomy = Taxonomy::create([
            'name' => 'Skills',
            'slug' => 'skill',
            'description' => 'Technical and soft skills required for jobs',
            'type' => 'skill',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 2,
        ]);

        $skills = [
            // Programming Languages
            'JavaScript', 'Python', 'Java', 'PHP', 'C#', 'C++', 'Ruby', 'Go', 'Rust', 'Swift',
            'Kotlin', 'TypeScript', 'SQL', 'R', 'MATLAB', 'Scala', 'Perl', 'Objective-C',
            
            // Web Technologies
            'HTML', 'CSS', 'React', 'Angular', 'Vue.js', 'Node.js', 'Express.js', 'Laravel',
            'Django', 'Flask', 'Spring Boot', 'ASP.NET', 'jQuery', 'Bootstrap', 'Tailwind CSS',
            
            // Databases
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis', 'SQLite', 'Oracle', 'SQL Server',
            'Elasticsearch', 'DynamoDB', 'Cassandra',
            
            // Cloud & DevOps
            'AWS', 'Azure', 'Google Cloud', 'Docker', 'Kubernetes', 'Jenkins', 'GitLab CI',
            'Terraform', 'Ansible', 'Chef', 'Puppet', 'Nginx', 'Apache',
            
            // Data & Analytics
            'Machine Learning', 'Data Analysis', 'Power BI', 'Tableau', 'Excel', 'Pandas',
            'NumPy', 'TensorFlow', 'PyTorch', 'Spark', 'Hadoop',
            
            // Soft Skills
            'Communication', 'Leadership', 'Problem Solving', 'Team Work', 'Project Management',
            'Time Management', 'Critical Thinking', 'Creativity', 'Adaptability', 'Negotiation',
            
            // Design Skills
            'Adobe Photoshop', 'Adobe Illustrator', 'Figma', 'Sketch', 'InVision', 'Canva',
            'AutoCAD', 'SolidWorks', 'Blender', '3D Modeling',
        ];

        foreach ($skills as $index => $skill) {
            Term::create([
                'taxonomy_id' => $skillsTaxonomy->id,
                'name' => $skill,
                'slug' => \Str::slug($skill),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Industries Taxonomy
        $industriesTaxonomy = Taxonomy::create([
            'name' => 'Industries',
            'slug' => 'industry',
            'description' => 'Business industries and sectors',
            'type' => 'industry',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 3,
        ]);

        $industries = [
            'Technology', 'Healthcare', 'Finance', 'Education', 'Retail', 'Manufacturing',
            'Construction', 'Real Estate', 'Transportation', 'Hospitality', 'Media',
            'Entertainment', 'Non-profit', 'Government', 'Agriculture', 'Energy',
            'Telecommunications', 'Automotive', 'Aerospace', 'Pharmaceutical',
            'Biotechnology', 'Consulting', 'Legal', 'Insurance', 'Banking',
        ];

        foreach ($industries as $index => $industry) {
            Term::create([
                'taxonomy_id' => $industriesTaxonomy->id,
                'name' => $industry,
                'slug' => \Str::slug($industry),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Experience Levels Taxonomy
        $experienceTaxonomy = Taxonomy::create([
            'name' => 'Experience Levels',
            'slug' => 'experience_level',
            'description' => 'Job experience level requirements',
            'type' => 'experience_level',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 4,
        ]);

        $experienceLevels = [
            'Entry Level (0-1 years)',
            'Junior (1-3 years)',
            'Mid-Level (3-5 years)',
            'Senior (5-8 years)',
            'Lead (8-12 years)',
            'Principal/Architect (12+ years)',
            'Executive/C-Level',
        ];

        foreach ($experienceLevels as $index => $level) {
            Term::create([
                'taxonomy_id' => $experienceTaxonomy->id,
                'name' => $level,
                'slug' => \Str::slug($level),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Job Types Taxonomy
        $jobTypesTaxonomy = Taxonomy::create([
            'name' => 'Job Types',
            'slug' => 'job_type',
            'description' => 'Employment types and work arrangements',
            'type' => 'job_type',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 5,
        ]);

        $jobTypes = [
            'Full-time',
            'Part-time',
            'Contract',
            'Freelance',
            'Temporary',
            'Internship',
            'Remote',
            'Hybrid',
            'On-site',
        ];

        foreach ($jobTypes as $index => $type) {
            Term::create([
                'taxonomy_id' => $jobTypesTaxonomy->id,
                'name' => $type,
                'slug' => \Str::slug($type),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Benefits Taxonomy
        $benefitsTaxonomy = Taxonomy::create([
            'name' => 'Benefits',
            'slug' => 'benefit',
            'description' => 'Job benefits and perks',
            'type' => 'benefit',
            'is_hierarchical' => false,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 6,
        ]);

        $benefits = [
            'Health Insurance',
            'Dental Insurance',
            'Vision Insurance',
            '401(k) Retirement Plan',
            'Paid Time Off',
            'Sick Leave',
            'Maternity/Paternity Leave',
            'Flexible Schedule',
            'Remote Work Options',
            'Professional Development',
            'Training Budget',
            'Conference Attendance',
            'Gym Membership',
            'Free Meals',
            'Transportation Allowance',
            'Stock Options',
            'Bonus Program',
            'Life Insurance',
            'Disability Insurance',
            'Employee Discounts',
        ];

        foreach ($benefits as $index => $benefit) {
            Term::create([
                'taxonomy_id' => $benefitsTaxonomy->id,
                'name' => $benefit,
                'slug' => \Str::slug($benefit),
                'is_active' => true,
                'sort_order' => $index + 1,
                'level' => 0,
            ]);
        }

        // Locations Taxonomy
        $locationsTaxonomy = Taxonomy::create([
            'name' => 'Locations',
            'slug' => 'location',
            'description' => 'Job locations and regions',
            'type' => 'location',
            'is_hierarchical' => true,
            'is_active' => true,
            'is_public' => true,
            'sort_order' => 7,
        ]);

        $locations = [
            'United States' => [
                'California', 'New York', 'Texas', 'Florida', 'Illinois', 'Pennsylvania',
                'Ohio', 'Michigan', 'Georgia', 'North Carolina', 'New Jersey', 'Virginia',
            ],
            'Canada' => [
                'Ontario', 'Quebec', 'British Columbia', 'Alberta', 'Manitoba', 'Saskatchewan',
            ],
            'United Kingdom' => [
                'England', 'Scotland', 'Wales', 'Northern Ireland',
            ],
            'Germany' => [
                'Berlin', 'Munich', 'Hamburg', 'Frankfurt', 'Cologne', 'Stuttgart',
            ],
        ];

        foreach ($locations as $country => $regions) {
            $countryTerm = Term::create([
                'taxonomy_id' => $locationsTaxonomy->id,
                'name' => $country,
                'slug' => \Str::slug($country),
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 0,
                'level' => 0,
            ]);

            foreach ($regions as $index => $region) {
                Term::create([
                    'taxonomy_id' => $locationsTaxonomy->id,
                    'name' => $region,
                    'slug' => \Str::slug($region),
                    'parent_id' => $countryTerm->id,
                    'is_active' => true,
                    'sort_order' => $index + 1,
                    'level' => 1,
                    'path' => $country,
                ]);
            }
        }

        $this->command->info('Taxonomy system seeded successfully!');
        $this->command->info('Created ' . Taxonomy::count() . ' taxonomies');
        $this->command->info('Created ' . Term::count() . ' terms');
    }
}
