<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfessionCategory;
use App\Models\ProfessionCategoryTranslation;

class ProfessionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Available languages in the system
        $languages = ['en', 'lt', 'ru', 'pl', 'de', 'fr', 'es', 'zh', 'ar', 'pt', 'tr', 'it', 'ja', 'hi'];

        // Main profession categories based on Lithuanian/International standards
        $categories = [
            [
                'code' => '1',
                'level' => 1,
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Managers',
                        'description' => 'Managers plan, direct and coordinate the overall activities of enterprises, governments and other organizations, or of organizational units within them, and formulate and review their policies, laws, rules and regulations.'
                    ],
                    'lt' => [
                        'name' => 'Vadovai',
                        'description' => 'Vadovai planuoja, valdo ir koordinuoja įmonių, vyriausybių ir kitų organizacijų ar jų padalinių bendrą veiklą, formuoja ir peržiūri jų politiką, įstatymus, taisykles ir reglamentus.'
                    ]
                ]
            ],
            [
                'code' => '2',
                'level' => 1,
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Professionals',
                        'description' => 'Professionals increase the existing stock of knowledge; apply scientific or artistic concepts and theories; teach about the foregoing; or engage in any combination of these activities.'
                    ],
                    'lt' => [
                        'name' => 'Specialistai',
                        'description' => 'Specialistai didina esamų žinių atsargas; taiko mokslinius ar meninius konceptus ir teorijas; moko apie tai; arba užsiima bet kokiu šių veiklų deriniu.'
                    ]
                ]
            ],
            [
                'code' => '3',
                'level' => 1,
                'sort_order' => 3,
                'translations' => [
                    'en' => [
                        'name' => 'Technicians and Associate Professionals',
                        'description' => 'Technicians and associate professionals perform mostly technical and related tasks connected with research and the application of scientific or artistic concepts and operational methods.'
                    ],
                    'lt' => [
                        'name' => 'Technikai ir asocijuoti specialistai',
                        'description' => 'Technikai ir asocijuoti specialistai atlieka daugiausia techninius ir susijusius darbus, susijusius su tyrimais ir mokslinių ar meninių konceptų bei veiklos metodų taikymu.'
                    ]
                ]
            ],
            [
                'code' => '4',
                'level' => 1,
                'sort_order' => 4,
                'translations' => [
                    'en' => [
                        'name' => 'Clerical Support Workers',
                        'description' => 'Clerical support workers record, organize, store, compute and retrieve information, and perform a number of clerical duties in connection with money-handling operations, travel arrangements, requests for information and appointments.'
                    ],
                    'lt' => [
                        'name' => 'Raštinės darbuotojai',
                        'description' => 'Raštinės darbuotojai registruoja, organizuoja, saugo, apskaičiuoja ir išgauna informaciją bei atlieka daugelį raštinės pareigų, susijusių su pinigų tvarkymo operacijomis, kelionių organizavimu, informacijos užklausomis ir susitikimų organizavimu.'
                    ]
                ]
            ],
            [
                'code' => '5',
                'level' => 1,
                'sort_order' => 5,
                'translations' => [
                    'en' => [
                        'name' => 'Services and Sales Workers',
                        'description' => 'Service and sales workers provide personal and protective services related to travel, housekeeping, catering, personal care, protection against fire and unlawful acts; or demonstrate and sell goods in wholesale or retail shops and similar establishments.'
                    ],
                    'lt' => [
                        'name' => 'Paslaugų ir prekybos darbuotojai',
                        'description' => 'Paslaugų ir prekybos darbuotojai teikia asmens ir apsaugos paslaugas, susijusias su kelionėmis, namų tvarkymu, maitinimu, asmens priežiūra, apsauga nuo gaisro ir neteisėtų veiksmų; arba demonstruoja ir parduoda prekes didmeninės ar mažmeninės prekybos parduotuvėse ir panašiose įstaigose.'
                    ]
                ]
            ],
            [
                'code' => '6',
                'level' => 1,
                'sort_order' => 6,
                'translations' => [
                    'en' => [
                        'name' => 'Skilled Agricultural, Forestry and Fishery Workers',
                        'description' => 'Skilled agricultural, forestry and fishery workers grow and harvest field or tree and shrub crops; gather wild fruits and plants; breed, tend or hunt animals; produce a variety of animal husbandry products; cultivate, conserve and exploit forests; breed or catch fish; cultivate or gather other forms of aquatic life.'
                    ],
                    'lt' => [
                        'name' => 'Kvalifikuoti žemės ūkio, miškų ūkio ir žuvininkystės darbuotojai',
                        'description' => 'Kvalifikuoti žemės ūkio, miškų ūkio ir žuvininkystės darbuotojai augina ir nukerpa laukų ar medžių bei krūmų derlių; renka laukinius vaisius ir augalus; veisia, globoja ar medžioja gyvūnus; gamina įvairius gyvulininkystės produktus; kultivuoja, saugo ir naudoja miškus; veisia ar gaudo žuvis; kultivuoja ar renka kitas vandens gyvybės formas.'
                    ]
                ]
            ],
            [
                'code' => '7',
                'level' => 1,
                'sort_order' => 7,
                'translations' => [
                    'en' => [
                        'name' => 'Craft and Related Trades Workers',
                        'description' => 'Craft and related trades workers apply specific knowledge and skills to construct and maintain buildings; form metal; erect metal structures; set machine tools; fit, maintain and repair machinery and equipment; print and make paper products; produce and process food products, textiles, clothing, leather, fur and footwear products; perform other related tasks.'
                    ],
                    'lt' => [
                        'name' => 'Amatininkai ir susiję prekybos darbuotojai',
                        'description' => 'Amatininkai ir susiję prekybos darbuotojai taiko specialias žinias ir įgūdžius pastatų statybai ir priežiūrai; metalo formavimui; metalo konstrukcijų statybai; staklių nustatymui; mašinų ir įrangos montavimui, priežiūrai ir remontui; spausdinimui ir popieriaus produktų gamybai; maisto produktų, tekstilės, drabužių, odos, kailio ir avalynės produktų gamybai ir apdorojimui; kitų susijusių užduočių atlikimui.'
                    ]
                ]
            ],
            [
                'code' => '8',
                'level' => 1,
                'sort_order' => 8,
                'translations' => [
                    'en' => [
                        'name' => 'Plant and Machine Operators and Assemblers',
                        'description' => 'Plant and machine operators and assemblers operate and monitor industrial and agricultural machinery and equipment; drive and operate trains, motor vehicles and mobile plant and equipment; or assemble products from component parts according to strict specifications and procedures.'
                    ],
                    'lt' => [
                        'name' => 'Įrenginių ir mašinų operatoriai bei surinkėjai',
                        'description' => 'Įrenginių ir mašinų operatoriai bei surinkėjai valdo ir stebi pramonės ir žemės ūkio mašinas ir įrangą; vairuoja ir valdo traukinius, variklinius transporto priemones ir mobilią įrangą; arba surenka produktus iš komponentų dalių pagal griežtas specifikacijas ir procedūras.'
                    ]
                ]
            ],
            [
                'code' => '9',
                'level' => 1,
                'sort_order' => 9,
                'translations' => [
                    'en' => [
                        'name' => 'Elementary Occupations',
                        'description' => 'Workers in elementary occupations perform simple and routine tasks which mainly require the use of hand-held tools and often some physical effort.'
                    ],
                    'lt' => [
                        'name' => 'Pagalbinės profesijos',
                        'description' => 'Pagalbinių profesijų darbuotojai atlieka paprastus ir rutininius darbus, kuriuos daugiausia reikia atlikti rankiniais įrankiais ir dažnai reikia fizinių pastangų.'
                    ]
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            // Create the main category
            $category = ProfessionCategory::create([
                'code' => $categoryData['code'],
                'level' => $categoryData['level'],
                'sort_order' => $categoryData['sort_order'],
                'is_active' => true,
                'metadata' => [
                    'icon' => 'fas fa-briefcase',
                    'color' => '#' . substr(md5($categoryData['code']), 0, 6)
                ]
            ]);

            // Create translations for all languages
            foreach ($languages as $lang) {
                $translationData = $categoryData['translations'][$lang] ?? $categoryData['translations']['en'];
                
                // For languages not provided, use Google Translate-like fallback or keep English
                if (!isset($categoryData['translations'][$lang])) {
                    $translationData = $categoryData['translations']['en'];
                }

                ProfessionCategoryTranslation::create([
                    'profession_category_id' => $category->id,
                    'locale' => $lang,
                    'name' => $translationData['name'],
                    'description' => $translationData['description']
                ]);
            }
        }

        // Create some subcategories for main categories
        $subcategories = [
            // Managers subcategories
            [
                'code' => '11',
                'parent_code' => '1',
                'level' => 2,
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Chief Executives, Senior Officials and Legislators',
                        'description' => 'Chief executives, senior officials and legislators determine and formulate policies and direct the overall activities of commercial, industrial, governmental and other organizational entities, or are elected or appointed to legislatures.'
                    ],
                    'lt' => [
                        'name' => 'Generaliniai direktoriai, aukšti pareigūnai ir įstatymų leidėjai',
                        'description' => 'Generaliniai direktoriai, aukšti pareigūnai ir įstatymų leidėjai nustato ir formuoja politiką bei valdo bendrą komercinių, pramonės, vyriausybės ir kitų organizacinių subjektų veiklą, arba yra renkami ar skiriami į įstatymų leidybos organus.'
                    ]
                ]
            ],
            [
                'code' => '12',
                'parent_code' => '1',
                'level' => 2,
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Administrative and Commercial Managers',
                        'description' => 'Administrative and commercial managers plan, direct and coordinate the administrative and commercial activities of enterprises and organizations.'
                    ],
                    'lt' => [
                        'name' => 'Administracijos ir komerciniai vadovai',
                        'description' => 'Administracijos ir komerciniai vadovai planuoja, valdo ir koordinuoja įmonių ir organizacijų administracines ir komercines veiklas.'
                    ]
                ]
            ],
            // Professionals subcategories
            [
                'code' => '21',
                'parent_code' => '2',
                'level' => 2,
                'sort_order' => 1,
                'translations' => [
                    'en' => [
                        'name' => 'Science and Engineering Professionals',
                        'description' => 'Science and engineering professionals conduct research; improve or develop concepts, theories and operational methods; or apply knowledge of natural sciences, engineering, agriculture, forestry and fishery.'
                    ],
                    'lt' => [
                        'name' => 'Mokslo ir inžinerijos specialistai',
                        'description' => 'Mokslo ir inžinerijos specialistai atlieka tyrimus; tobulina ar plėtoja koncepcijas, teorijas ir veiklos metodus; arba taiko gamtos mokslų, inžinerijos, žemės ūkio, miškų ūkio ir žuvininkystės žinias.'
                    ]
                ]
            ],
            [
                'code' => '22',
                'parent_code' => '2',
                'level' => 2,
                'sort_order' => 2,
                'translations' => [
                    'en' => [
                        'name' => 'Health Professionals',
                        'description' => 'Health professionals plan, manage and provide medical, dental, pharmaceutical, nursing and midwifery services, and related health care services.'
                    ],
                    'lt' => [
                        'name' => 'Sveikatos specialistai',
                        'description' => 'Sveikatos specialistai planuoja, valdo ir teikia medicinos, odontologijos, farmacijos, slaugos ir akušerijos paslaugas bei susijusias sveikatos priežiūros paslaugas.'
                    ]
                ]
            ]
        ];

        foreach ($subcategories as $subcategoryData) {
            // Find parent category
            $parentCategory = ProfessionCategory::where('code', $subcategoryData['parent_code'])->first();
            
            if ($parentCategory) {
                // Create the subcategory
                $subcategory = ProfessionCategory::create([
                    'code' => $subcategoryData['code'],
                    'parent_id' => $parentCategory->id,
                    'level' => $subcategoryData['level'],
                    'sort_order' => $subcategoryData['sort_order'],
                    'is_active' => true,
                    'metadata' => [
                        'icon' => 'fas fa-briefcase',
                        'color' => '#' . substr(md5($subcategoryData['code']), 0, 6)
                    ]
                ]);

                // Create translations for all languages
                foreach ($languages as $lang) {
                    $translationData = $subcategoryData['translations'][$lang] ?? $subcategoryData['translations']['en'];
                    
                    ProfessionCategoryTranslation::create([
                        'profession_category_id' => $subcategory->id,
                        'locale' => $lang,
                        'name' => $translationData['name'],
                        'description' => $translationData['description']
                    ]);
                }
            }
        }
    }
} 