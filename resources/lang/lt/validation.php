    /*
    |--------------------------------------------------------------------------
    | Admin Validation Messages
    |--------------------------------------------------------------------------
    |
    | Custom validation messages for admin operations in Lithuanian
    |
    */
    'admin' => [
        'first_name_required' => 'Vardas yra privalomas',
        'first_name_format' => 'Vardas gali turėti tik raides ir tarpus',
        'first_name_min' => 'Vardas turi būti mažiausiai 2 simbolių',
        'last_name_format' => 'Pavardė gali turėti tik raides ir tarpus',
        'name_required' => 'Pilnas vardas yra privalomas',
        
        'email_required' => 'El. pašto adresas yra privalomas',
        'email_format' => 'Įveskite teisingą el. pašto adresą',
        'email_unique' => 'Šis el. pašto adresas jau užregistruotas',
        'email_unique_update' => 'Šis el. pašto adresas jau naudojamas kito administratoriaus',
        'email_domain_not_allowed' => 'El. pašto domenas neleidžiamas administratoriaus paskyroms',
        
        'password_required' => 'Slaptažodis yra privalomas',
        'password_min' => 'Slaptažodis turi būti mažiausiai 12 simbolių',
        'password_complexity' => 'Slaptažodis turi turėti didžiąsias, mažąsias raides, skaičius ir specialius simbolius',
        'password_confirmation' => 'Slaptažodžio patvirtinimas nesutampa',
        'password_confirmation_required' => 'Slaptažodžio patvirtinimas yra privalomas',
        'password_match' => 'Slaptažodžio patvirtinimas turi sutapti su slaptažodžiu',
        'password_too_common' => 'Šis slaptažodis per paprastas, pasirinkite saugesnį',
        
        'role_required' => 'Vaidmuo yra privalomas',
        'role_invalid' => 'Pasirinktas vaidmuo neteisingas',
        'role_not_allowed' => 'Jums neleidžiama priskirti šio vaidmens',
        'max_super_admins' => 'Pasiektas maksimalus super administratorių skaičius',
        
        'phone_format' => 'Telefono numerio formatas neteisingas',
        'status_boolean' => 'Statusas turi būti aktyvus arba neaktyvus',
        
        'dob_before_today' => 'Gimimo data turi būti anksčiau nei šiandien',
        'dob_after_1900' => 'Gimimo data turi būti vėliau nei 1900 metai',
        'gender_invalid' => 'Pasirinkite teisingą lyties variantą',
        
        'cannot_deactivate_self' => 'Negalite išjungti savo paskyrų',
    ],

    /*
    |--------------------------------------------------------------------------
    | Master Data Validation Messages
    |--------------------------------------------------------------------------
    |
    | Custom validation messages for master data operations in Lithuanian
    |
    */
    'master_data' => [
        'name_required' => 'Pavadinimas yra privalomas',
        'name_string' => 'Pavadinimas turi būti tekstas',
        'name_max' => 'Pavadinimas negali viršyti 255 simbolių',
        'status_invalid' => 'Statusas turi būti aktyvus arba neaktyvus',
        'sort_order_integer' => 'Rūšiavimo tvarka turi būti skaičius',
        'sort_order_min' => 'Rūšiavimo tvarka negali būti neigiama',
        'description_max' => 'Aprašymas negali viršyti 1000 simbolių',
    ],

    /*
    |--------------------------------------------------------------------------
    | Business Logic Validation Messages
    |--------------------------------------------------------------------------
    |
    | Custom validation messages for business logic operations in Lithuanian
    |
    */
    'business_logic' => [
        'title_required' => 'Pavadinimas yra privalomas',
        'title_string' => 'Pavadinimas turi būti tekstas',
        'title_max' => 'Pavadinimas negali viršyti 255 simbolių',
        'status_invalid' => 'Statusas neteisingas',
    ], 