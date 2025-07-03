<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfessionCategory;
use App\Models\Profession;
use App\Models\ProfessionTranslation;

class ProfessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Available languages in the system
        $languages = ['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi'];

        // Sample professions data - this should be expanded with real Lithuanian profession data
        $professions = [
            // Managers (Category 1)
            [
                'code' => '1111',
                'category_code' => '11',
                'isco_code' => '1111',
                'skill_level' => 'High',
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Chief Executive Officer',
                        'description' => 'Plans, directs and coordinates the overall activities of an enterprise or organization, formulates policies and has ultimate responsibility for all business operations.',
                        'skills_required' => ['Leadership', 'Strategic Planning', 'Decision Making', 'Communication', 'Business Acumen'],
                        'education_requirements' => ['Bachelor\'s degree in Business Administration or related field', 'Master\'s degree preferred', 'Extensive management experience']
                    ],
                    'lt' => [
                        'name' => 'Generalinis direktorius',
                        'description' => 'Planuoja, valdo ir koordinuoja bendrą įmonės ar organizacijos veiklą, formuoja politiką ir turi galutinę atsakomybę už visą verslo veiklą.',
                        'skills_required' => ['Lyderystė', 'Strateginis planavimas', 'Sprendimų priėmimas', 'Komunikacija', 'Verslo supratimas'],
                        'education_requirements' => ['Bakalauro laipsnis verslo administravimo ar susijusioje srityje', 'Magistro laipsnis pageidautinas', 'Plati vadybos patirtis']
                    ]
                ]
            ],
            [
                'code' => '1112',
                'category_code' => '11',
                'isco_code' => '1112',
                'skill_level' => 'High', 
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Senior Government Official',
                        'description' => 'Plans, directs and coordinates the administrative and policy activities of government departments and agencies.',
                        'skills_required' => ['Public Administration', 'Policy Development', 'Leadership', 'Communication', 'Analytical Thinking'],
                        'education_requirements' => ['Bachelor\'s degree in Public Administration, Political Science or related field', 'Master\'s degree preferred', 'Extensive government experience']
                    ],
                    'lt' => [
                        'name' => 'Aukštas vyriausybės pareigūnas',
                        'description' => 'Planuoja, valdo ir koordinuoja vyriausybės departamentų ir agentūrų administracinę ir politikos veiklą.',
                        'skills_required' => ['Viešasis administravimas', 'Politikos formavimas', 'Lyderystė', 'Komunikacija', 'Analitinis mąstymas'],
                        'education_requirements' => ['Bakalauro laipsnis viešojo administravimo, politikos mokslų ar susijusioje srityje', 'Magistro laipsnis pageidautinas', 'Plati vyriausybės patirtis']
                    ]
                ]
            ],
            // Professionals (Category 2)
            [
                'code' => '2111',
                'category_code' => '21',
                'isco_code' => '2111',
                'skill_level' => 'High',
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Physicist',
                        'description' => 'Conducts research and develops theories concerning physical phenomena, makes practical applications of such knowledge and teaches physics.',
                        'skills_required' => ['Research', 'Mathematical Analysis', 'Scientific Method', 'Data Analysis', 'Problem Solving'],
                        'education_requirements' => ['Master\'s or Doctoral degree in Physics', 'Research experience', 'Published scientific work preferred']
                    ],
                    'lt' => [
                        'name' => 'Fizikas',
                        'description' => 'Atlieka tyrimus ir kuria teorijas apie fizinius reiškinius, taiko tokias žinias praktiškai ir dėsto fiziką.',
                        'skills_required' => ['Tyrimai', 'Matematinė analizė', 'Mokslinis metodas', 'Duomenų analizė', 'Problemų sprendimas'],
                        'education_requirements' => ['Magistro ar daktaro laipsnis fizikos srityje', 'Tyrimų patirtis', 'Publikuoti mokslo darbai pageidautini']
                    ]
                ]
            ],
            [
                'code' => '2112',
                'category_code' => '21',
                'isco_code' => '2112',
                'skill_level' => 'High',
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Meteorologist',
                        'description' => 'Studies and forecasts weather and atmospheric conditions, conducts research into weather patterns and climate change.',
                        'skills_required' => ['Weather Analysis', 'Atmospheric Science', 'Data Interpretation', 'Computer Modeling', 'Statistical Analysis'],
                        'education_requirements' => ['Bachelor\'s degree in Meteorology, Atmospheric Science or related field', 'Master\'s degree for research positions', 'Professional certification']
                    ],
                    'lt' => [
                        'name' => 'Meteorologas',
                        'description' => 'Tiria ir prognozuoja orus bei atmosferos sąlygas, atlieka orų šablonų ir klimato kaitos tyrimus.',
                        'skills_required' => ['Orų analizė', 'Atmosferos mokslas', 'Duomenų interpretacija', 'Kompiuterinis modeliavimas', 'Statistinė analizė'],
                        'education_requirements' => ['Bakalauro laipsnis meteorologijos, atmosferos mokslų ar susijusioje srityje', 'Magistro laipsnis tyrimų pozicijoms', 'Profesinis sertifikavimas']
                    ]
                ]
            ],
            // Health Professionals
            [
                'code' => '2211',
                'category_code' => '22',
                'isco_code' => '2211',
                'skill_level' => 'High',
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'General Medical Practitioner',
                        'description' => 'Diagnoses, treats and prevents human illness, injury and other physical and mental impairments by applying the principles and procedures of modern medicine.',
                        'skills_required' => ['Medical Diagnosis', 'Patient Care', 'Clinical Skills', 'Communication', 'Emergency Medicine'],
                        'education_requirements' => ['Doctor of Medicine degree', 'Medical residency training', 'Medical license', 'Continuing medical education']
                    ],
                    'lt' => [
                        'name' => 'Bendrosios praktikos gydytojas',
                        'description' => 'Diagnozuoja, gydo ir apsaugo nuo žmonių ligų, sužalojimų ir kitų fizinių bei psichinių sutrikimų, taikydamas šiuolaikinės medicinos principus ir procedūras.',
                        'skills_required' => ['Medicinos diagnostika', 'Pacientų priežiūra', 'Klinikiniai įgūdžiai', 'Komunikacija', 'Skubi medicina'],
                        'education_requirements' => ['Medicinos daktaro laipsnis', 'Medicinos rezidentūros mokymas', 'Medicinos licencija', 'Tęstinis medicinos mokymas']
                    ]
                ]
            ],
            // Software Developers (very relevant for job portal)
            [
                'code' => '2512',
                'category_code' => '25',
                'isco_code' => '2512',
                'skill_level' => 'High',
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Software Developer',
                        'description' => 'Researches, designs, writes and tests computer programs and software applications for computers and mobile devices.',
                        'skills_required' => ['Programming Languages', 'Software Design', 'Problem Solving', 'Testing', 'Version Control', 'Database Management'],
                        'education_requirements' => ['Bachelor\'s degree in Computer Science or related field', 'Portfolio of projects', 'Relevant certifications']
                    ],
                    'lt' => [
                        'name' => 'Programinės įrangos kūrėjas',
                        'description' => 'Tiria, projektuoja, rašo ir testuoja kompiuterių programas bei programinės įrangos aplikacijas kompiuteriams ir mobiliesiems įrenginiams.',
                        'skills_required' => ['Programavimo kalbos', 'Programinės įrangos dizainas', 'Problemų sprendimas', 'Testavimas', 'Versijų kontrolė', 'Duomenų bazių valdymas'],
                        'education_requirements' => ['Bakalauro laipsnis kompiuterių mokslo ar susijusioje srityje', 'Projektų portfelis', 'Aktualūs sertifikatai']
                    ]
                ]
            ]
        ];

        foreach ($professions as $professionData) {
            // Find the category
            $category = ProfessionCategory::where('code', $professionData['category_code'])->first();
            
            if ($category) {
                // Create the profession
                $profession = Profession::create([
                    'code' => $professionData['code'],
                    'category_id' => $category->id,
                    'isco_code' => $professionData['isco_code'],
                    'skill_level' => $professionData['skill_level'],
                    'is_active' => true,
                    'sort_order' => $professionData['sort_order'],
                    'metadata' => [
                        'difficulty_level' => $professionData['skill_level'],
                        'in_high_demand' => in_array($professionData['code'], ['2512']) // Software developers in high demand
                    ]
                ]);

                // Create translations for all languages
                foreach ($languages as $lang) {
                    $translationData = $professionData['translations'][$lang] ?? $professionData['translations']['en'];
                    
                    // For languages not provided, use English as fallback
                    if (!isset($professionData['translations'][$lang])) {
                        $translationData = $professionData['translations']['en'];
                    }

                    ProfessionTranslation::create([
                        'profession_id' => $profession->id,
                        'locale' => $lang,
                        'name' => $translationData['name'],
                        'description' => $translationData['description'],
                        'skills_required' => $translationData['skills_required'],
                        'education_requirements' => $translationData['education_requirements']
                    ]);
                }
            }
        }
    }
} 