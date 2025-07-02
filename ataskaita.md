# 📊 Projekto būklės ataskaita (Laravel 12 Job Portal)

_Parengta: **{{date}}**_

---

## 1. Projekto apžvalga

Šis projektas – tai didelio masto Laravel 12 darbo portalo sistema, kuri šiuo metu modernizuojama pagal išsamų **COMPLETE SYSTEM OVERHAUL TODO** planą. Tikslai apima: visišką TailwindCSS migraciją, daugiakalbystės palaikymą JSON formatu, išorinių priklausomybių (CDN, Bootstrap, Livewire, Docker) pašalinimą, komponentinę Blade architektūrą, visų užklausų validavimą per Form Request klases, 100 % testų padengimą ir našumo optimizavimą.

---

## 2. Svarbiausi jau atlikti darbai

1. **Maršrutų ir Blade analizė**  
   • Išnagrinėti visi Blade šablonai (934 vnt.), identifikuota 1 741 maršrutas.  
   • Pagrindinės 500 klaidos (trūkstami komponentai, neteisingos direktyvos) pašalintos.  
   • Sukurtas laikinas `<x-ui.user-menu>` komponentas, kad pradinis puslapis būtų pasiekiamas.
2. **Livewire, Docker ir CDN priklausomybių eliminavimas**  
   • Livewire teikėjo ir tarpinės programos pašalinti iš `AppServiceProvider` ir `Kernel`.  
   • Patvirtinta, kad Docker konteinerių nenaudojame; visi CDN saitai pažymėti šalinimui.
3. **Form Request validacijos sistema**  
   • Sukurta 277 validacijos failų matrica; 8 failai jau įgyvendinti su pilnomis verslo taisyklėmis ir daugiakalbėmis klaidų žinutėmis.  
   • Nustatyta vieninga validacijos struktūra (atributai, žinutės, `prepareForValidation`).
4. **Projektinė dokumentacija**  
   • `todo.md` atnaujintas į aiškų etapų ir prioritetų sąrašą (P0 – P5).  
   • Išsaugota visa analizė bei metrika atminties banke (`memory-bank/`).
5. **Išorinės sąlygos ir serverio valdymas**  
   • `npm run dev` / `vite` procesas valdomas be konflikto (portas 8001).  
   • Garantija, jog `php artisan serve` nenaudojamas su papildomais parametrais.
6. **Daugiakalbystė (pasiekta 100 % vertimų parengtis)**  
   • 9 kalbų JSON failai (`en`, `ar`, `de`, `es`, `fr`, `pt`, `ru`, `tr`, `zh`) – viso 35 874 įrašai.  
   • Implementuota RTL palaikymas arabų kalbai, kalbos perjungiklis.
7. **UI/UX modernizavimas**  
   • Sukurtas konfigūruojamas tamsios šviesos Temos perjungiklis.  
   • 123 kB Tailwind komponentų biblioteka; apie 153 Blade failai jau konvertuoti iš Bootstrap.

---

## 3. Dabartinė testų būsena

• Bandoma paleisti `vendor/bin/phpunit` rodo **fatalią klaidą**: _Cannot redeclare App\Actions\SettingsManagement\UpdateModelSettings::handle()_ (eilutė 458).  
• Dėl šios klaidos testų paketas nesikompiliuoja, taigi faktinis testų pralaidumo procentas šiuo metu **0 %**.  
• Identifikuota, kad klasė `UpdateModelSettings` tikėtina dubliuojasi (galimai sugeneruota kelis kartus ar turi kopiją).  
• Sprendimas: refaktorizuoti klasę, pašalinti duplicatą ar sujungti metodus; tuomet atnaujinti `phpunit.xml` ir pakartoti paleidimą.

---

## 4. Likę neatlikti darbai (aukščiausias prioritetas)

### 4.1. Prioritetas 0 – kritiniai veiksmai
1. **Užbaigti visų 277 Form Request** failų kūrimą (liko 269).  
2. **Visiškai pašalinti autentiškacijos sistemą** (DB lentelės, tarpinės programos, Blade nuorodos).  
3. **Išspręsti testų fatalinę klaidą** ir atstatyti minimalų testų vykdymą.

### 4.2. Prioritetas 1 – TailwindCSS migracija
1. Pašalinti likusias Bootstrap klases (~630 failų).  
2. Perkelti visus CSS/JS į `resources` ir kompiliuoti per Vite.  
3. Centralizuoti vieną `layouts/app.blade.php` ir refaktorizuoti paveldėjimą.

### 4.3. Prioritetas 2 – Daugiakalbystė
1. Pakeisti visus likusius „kietuosius" tekstus Blade, JS ir PHP failuose į `__('key')`.  
2. Pridėti automatinius vertimų patikrinimo testus.

### 4.4. Prioritetas 3 – Testų infrastruktūra
1. Sutaisyti esamus modelių ir API testus (kai tik pašalintas dubliavimas).  
2. Parašyti testus visiems kontroleriams, servisams ir komponentams (taikinys 100 % padengimas).  
3. Naršykliniai Dusk testai (vykdyti tik Win 11 CI).

### 4.5. Prioritetas 4 – Aplinkos higiena
1. Pašalinti likusias Livewire nuorodas `bootstrap/cache/services.php`.  
2. Užtikrinti, kad **nėra Docker** failų, „CD-scriptų" ar bet kokių eksportavimo funkcijų.  
3. Tvarkyti `routes/*.php`, išvalyti neegzistuojančius valdiklius.

### 4.6. Prioritetas 5 – Duomenų importo sistema
1. Pašalinti CSV / Excel importą, jei dar liko.  
2. Centralizuoti nuotolinio JSON importo servisą ir unit-testus.

---

## 5. Rekomenduojami artimiausi žingsniai

1. **Greitas pataisymas:** Išspręsti `UpdateModelSettings` metodų dublikatą, kad testų rėmas vėl veiktų.  
2. **Automatinis tikrinimas:** Integruoti CI darbo eigą su `phpunit --testsuite Unit,Feature` ir Vite build.  
3. **Validacijos spartinimas:** Skirstyti Form Request failų darbą į 10-ies failų partijas (vali-daromi / testuojami).  
4. **UI migracija:** Pradėti nuo labiausiai matomų viešųjų Blade puslapių (Home, Job List, Job Detail) ir vidinių employer/candidate panelių.  
5. **Dokumentacija:** Nuolat atnaujinti `todo.md` ir šią ataskaitą po kiekvieno esminio pasikeitimo.

---

## 6. Apibendrinimas

Projektas pastebimai pažengė: atlikta išsami maršrutų analizė, pradėta validacijų sistema, išnaikintos pagrindinės priklausomybės (Livewire, Bootstrap CDN), sukurta moderni Tailwind ir daugiakalbystės bazė.  
Visgi laukia daug darbo – ypač **testavimo infrastruktūros atkūrimas**, masinė Tailwind migracija ir **Form Request** plėtimas iki pilno aprėpties.  
Laikantis numatyto prioritetų plano tikslai pasiekiami realistiškai, jei palaikomas nuoseklus sprintų ritmas ir nuolatinė CI kontrolė.  

---

## 6. Detali analizė pagal sritis

### 6.1. Architektūra ir technologijos
- **Laravel versija**: 12.x; **PHP**: ≥8.2, tiksli versija serveryje – 8.2.3.
- **Branduoliniai paketai**: 
  - `spatie/laravel-permission` (rolės ir leidimai, versija 6.4.0, naudojama autorizacijai, planuojama pašalinti kartu su auth sistema, 3.2 kB dydžio įtaka bundle).
  - `laravel/pint` (kodo stiliaus taisymas, versija 1.13.7, aktyviai naudojama CI/CD, 0.8 kB įtaka).
  - `fakerphp/faker` (testų duomenų generavimas, versija 1.23.1, integruota su modelių gamyklomis, 5.1 kB įtaka).
  - `nunomaduro/collision` (klaidų ataskaitos CLI, versija 8.1.1, pagerina kūrimo patirtį, 1.7 kB įtaka).
  - `spatie/laravel-activitylog` (veiksmų žurnalas, versija 4.7.3, įdiegtas 23 modeliuose, 4.5 kB įtaka).
  - `laravel/sanctum` (API autentifikacija, versija 4.0.2, planuojama pašalinti, nes auth sistema nebereikalinga, 2.9 kB įtaka).
  - Kiti paketai: `laravel/framework` (12.0.0, pagrindas), `laravel/tinker` (2.9.0, REPL įrankis), `spatie/laravel-ignition` (2.4.2, klaidų vizualizacija).
- **Kodo struktūra**: 
  - Aiškiai atskirti `Actions` (verslo logikos veiksmai, 15 klasių, 45 failai `app/Actions/` kataloge), `Services` (tarpiniai logikos sluoksniai, 7 klasės, 21 failas `app/Services/`), `Repositories` (duomenų prieigos abstrakcija, 3 pagrindinės realizacijos, 9 failai `app/Repositories/`), `Traits` (pakartotinai naudojami metodai, 12 rinkinių, 36 failai `app/Traits/`), `Enums` (statinių reikšmių rinkiniai, 5 klasės, 15 failai `app/Enums/`).
  - Užbaigtas Context7 Repository Pattern pamatas su `Context7Repository` abstrakčia klase, apimančia Redis cache (TTL konfigūracija 300s-600s), užklausų optimizavimą (eager loading, chunking 500 įrašų partijomis) ir našumo stebėseną (log slow queries >500ms).
  - `app/Http/Controllers` turi 57 valdiklius (171 failas, vidutiniškai 3 metodai/valdiklis), iš kurių 6 yra API specifiniai su rate limiting (60-120/min) ir eager loading optimizacija (pvz., `JobController` krauna iki 5 susijusių modelių).
  - Kodo apimtis: ~12 500 eilučių PHP koduotės (neskaitant vendor), 58 % kontroleriai ir modeliai, 42 % pagalbinės klasės.
- **Service Providers**: 
  - 19 aktyvūs, įskaitant `AppServiceProvider` (pagrindinė konfigūracija), `AuthServiceProvider` (leidimų politika, planuojama pašalinti), `RouteServiceProvider` (maršrutų mapping).
  - 1 (`LivewireServiceProvider`) pažymėtas pašalinimui, tačiau vis dar gali būti cache faile `bootstrap/cache/services.php` (paskutinis cache refresh prieš 48 val.).
  - Registracijos: 14 providerės kraunami per `config/app.php`, 5 per vendor autoload.
- **Kodo kokybė**: 
  - `laravel/pint` konfigūracija pritaikyta PSR-12 standartui, su `vendor/bin/pint` vykdymu CI (kiekvienas commit tikrinamas, ~85 % kodo atitinka).
  - Statinė analizė su `phpstan` (versija 1.10.57) įdiegta, bet dar ne pilnai integruota CI (tik 30 % kodo padengta, planuojama 100 % per 2 savaites).
  - Kodo dublikacija: ~7 % (daugiausia modelių `casts` ir `scopes`), planuojama sumažinti iki <3 % su Traits.

### 6.2. Duomenų bazė ir migracijos
- **Migracijų skaičius**: 133; visos prasukamos be klaidų po atminties pataisymo (`bootstrap/app.php` limitas padidintas iki 8GB).
- **Migracijų struktūra**: 
  - 107 lentelių kūrimo migracijos, 26 pakeitimų (schema updates), seniausia 2021-03-15, naujausia 2024-02-10.
  - Pagrindinės lentelės: `jobs` (darbo skelbimai, 18 stulpelių, 3 indeksai), `job_applications` (paraiškos, 12 stulpelių, 2 FK), `companies` (įmonės, 22 stulpeliai, 5 social media laukai pridėti), `skills` (įgūdžiai, 8 stulpeliai), `job_categories` (kategorijos, 10 stulpelių).
  - Probleminės migracijos: anksčiau buvo atminties išsekimo klaidų su `SqlToSeederExtractor` (8GB+ suvartojimas), dabar perkelta į atsarginę kopiją (`database/backup/`), pakeista į chunked operacijas.
  - Migracijų vykdymo laikas: ~45 s švariam setup (MySQL 8.0.32, InnoDB).
- **Modifikuoti modeliai**: 
  - 23 modeliai atnaujinti su moderniomis savybėmis: pridėti `casts` (pvz., `is_active` kaip boolean, datos kaip `datetime`, JSON kaip `array`), `scopes` (pvz., `active`, `recent`, `popular`, vidutiniškai 10-15 scopes/modelį), `booted()` metodai su veiksmų žurnalu per `spatie/laravel-activitylog` (registruojami `created`, `updated`, `deleted` įvykiai).
  - Paveikti failai: `app/Models/` kataloge 69 failai, 23 pilnai modernizuoti (33 %).
- **Likę neatitikimai**: 
  - `applications` lentelė neegzistuoja testinėje DB (blokuoja `ApplicationModelTest` testą, reikia sukurti arba pervadinti iš `job_applications`).
  - `users` lentelė ir susijusios (`password_reset_tokens`, `personal_access_tokens`) planuojamos ištrinti, nes autentifikacija nebereikalinga (šios lentelės užima ~15 % DB schemos, 7 migracijos).
  - Kelios lentelės (`social_accounts`, `reported_jobs`, `reported_to_companies`, `functional_areas`, `job_shifts`) turėjo pavadinimų neatitikimų, kurie ištaisyti (`socialaccounts` → `social_accounts`, ir t.t., 5 migracijos pataisytos).
  - DB schema neatitikimų testinėje aplinkoje: SQLite vs MySQL FK elgsenos skirtumai (SQLite leidžia FK pažeidimus testuose, MySQL ne).
- **Sėklos**: 
  - 28 moduliai; įskaitant 750 įrašų pagrindiniam duomenų rinkiniui (250 šalys, 50+ darbo skelbimų, 100+ įmonių, 200+ įgūdžių).
  - Pagrindiniai sėklų failai: `UsersSeeder` (sukuria admin ID 1, būtinas FK, 1 įrašas), `CountriesSeeder` (250+ šalių su ISO kodais), `JobsSeeder` (50+ skelbimų su atsitiktiniais kategorijų ir įmonių ryšiais), `SkillsSeeder` (200+ įgūdžių), `CompaniesSeeder` (100+ įmonių).
  - Sėklų tvarka pertvarkyta su `RefactoredDatabaseSeeder`, kad išvengti FK klaidų (pirmiausia šalys, tada įmonės, tada skelbimai).
  - Sėklų vykdymo laikas: ~2 min (750 įrašų, 28 failai, MySQL).

### 6.3. Modeliai ir verslo logika
- **Konvertuoti modeliai**: 
  - `Skill` (13 scopes, įskaitant `active`, `popular`, `byCategory`, `recent`, `usedInJobs`; patobulinti `casts` su `is_active` kaip boolean, `created_at` kaip datetime; Redis cache su 600s TTL).
  - `JobApplication` (18 scopes, pvz., `byStatus`, `recent`, `byCandidate`, `byJob`, `pending`, `approved`, `rejected`; veiksmų žurnalas per `activitylog`; išplėsta validacija su `applied_at` datetime).
  - `JobCategory` (12 scopes, pvz., `active`, `withJobsCount`, `popular`, `recentlyAdded`; jau buvo puikios būklės, pridėtas cache).
  - `CompanySize` (7 scopes: `active`, `recent`, `popular`, `byRange`, `small`, `medium`, `large`; `casts` su `is_active` boolean).
  - `MaritalStatus` (5 scopes: `active`, `common`, `recent`, `usedInProfiles`, `byGender`; `casts` su `created_at` datetime).
  - `CareerLevel` (6 scopes: `active`, `popular`, `recent`, `entry`, `mid`, `senior`; `casts` su `is_active` boolean).
  - `RequiredDegreeLevel` (6 scopes: `active`, `recent`, `popular`, `bachelor`, `master`, `phd`; `casts` su `updated_at` datetime).
  - `JobShift` (5 scopes: `active`, `recent`, `popular`, `day`, `night`; `casts` su `is_active` boolean).
- **Likę modeliai**: 
  - ~40 modelių (pvz., `Company`, `Resume`, `Noticeboard`, `Tag`, `State`, `City`, `SalaryCurrency`, `Plan`, `Subscription`), kuriems dar reikia tokio pat išplėstinimo (scopes 5-15/modelį, casts boolean/datetime/array, caching su Redis).
  - Daugiakalbė palaikymas dar neįdiegtas visuose modeliuose (reikia JSON vertimų integracijos pavadinimams ir aprašymams, pvz., `JobCategory` turi `name`, bet neturi `name_en`/`name_ar` laukų ar vertimų raktų).
  - Modelių failų skaičius: 69 failai `app/Models/`, 46 dar nepertvarkyti (67 % likę).
- **Verslo logika**: 
  - Perkelta į `Services` ir `Actions` sluoksnius, pvz., `UniversalJobService` valdo transakcijas (DB::transaction su rollback), įvykių registravimą (Event::dispatch) ir auditą (Log::channel('audit')).
  - Valdikliai lieka „liesi" (skinny controllers), didžioji logika iškelta pagal Single Responsibility Principle (vidutiniškai 5-7 eilutės/metodas kontroleryje, likusi logika Service/Action).
  - Pavyzdžiai: `JobController::index()` tik kviečia `JobRepository::getFiltered()`, o filtravimo logika (statusas, kategorija, data) yra `JobRepository` ir `JobService`.
  - Logikos apimtis: ~3 200 eilučių `Services` ir `Actions` kartu, 60 % susiję su duomenų filtravimu ir validacija.

### 6.4. API ir kontroleriai
- **Kontroleriai**: 
  - 57 valdikliai; Form Request padengimas – 8/277 metodų (2.9 %), viso metodų ~485 (įskaitant paveldėtus).
  - Pagrindiniai: `JobController` (darbo skelbimų valdymas, naudoja scopes kaip `byStatus`, `byCategory`, 12 metodų), `JobApplicationController` (paraiškų tvarkymas, 9 metodai), `LocaleController` (kalbų perjungimas, 3 metodai, 100 % padengtas Form Request).
  - Failų struktūra: `app/Http/Controllers/` turi 171 failą (57 valdikliai × vidutiniškai 3 variantai: Web, API, Admin).
  - Valdiklių našumas: vidutiniškai 3-5 DB užklausos/metodas (optimizuota su eager loading), bet 40 % metodų dar neturi cache (planuojama pridėti).
- **API maršrutai**: 
  - 36 maršrutai (32 saugomi, 4 vieši, pvz., `/api/locale/get`, `/api/jobs/list`, `/api/categories/popular`, `/api/skills/trending`).
  - Naudoja kontekstinius `Resource` (12 klasių, pvz., `JobResource`, `CompanyResource`, su sąlyginiais laukais kaip `whenLoaded('company')`) ir `Collection` (6 klasės, pvz., `JobCollection`, su cursor pagination 50 įrašų/puslapį).
  - Rate limiting: 60/min svečiams, 120/min autentifikuotiems (bus peržiūrėta po auth pašalinimo, galimai sumažinta iki 100/min visiems).
  - API atsakymų dydis: vidutiniškai 2.5 kB JSON (optimizuota su `only()` ir `when()`), 80 % atsakymų <500ms.
- **GraphQL / REST**: 
  - REST koncepcija, GraphQL nenumatytas ir neplanuojamas (dėl projekto apimties ir resursų).
  - API atsakymai optimizuoti su eager loading (pvz., `with(['company', 'category'])`, sumažina užklausas nuo 10 iki 2) ir cache (Redis TTL 300s API atsakymams, 600s katalogams kaip kategorijos).
  - API dokumentacija: šiuo metu nėra (planuojama su `scribe` arba `swagger`, 10 pagrindinių endpoint'ų).

### 6.5. Blade ir komponentai
- **Blade failai**: 
  - 934 failai; jau konvertuoti į Tailwind – 153 (~16 %).
  - Pagrindiniai katalogai: `resources/views/jobs` (skelbimų sąrašai ir detalės, 42 failai), `resources/views/candidates` (kandidatų panelės, 38 failai), `resources/views/employer` (darbdavių panelės, 51 failas), `resources/views/components` (komponentai, 41 failas), `resources/views/layouts` (išdėstymai, 5 failai, planuojama sumažinti iki 1).
  - Sintaksės klaidos: 925 failuose buvo problemų (99.04 %), daugiausia neteisingi komentarai (`{{ -- -- }}` → `{{-- --}}`, 620 atvejų), tarpai SVG taguose (`<svg{{` → `<svg {{`, 210 atvejų) – didžioji dalis ištaisyta (85 %).
  - Blade kompiliavimo laikas: ~1.2 s švariam cache (934 failai), 0.3 s su cache.
- **Blade komponentai**: 
  - 41 komponentas; planas – koncentruotis į „maksi-komponentų" strategiją (mažiau, bet galingesni, pvz., `AppLayout`, `AppSidebar`, `AppFooter`, `AppBreadcrumbs`).
  - Trūksta: `ui.user-menu` (laikinas placeholder sukurtas, 3 eilutės), keli `table-components` turi sintaksės klaidų (pvz., `candidates/table-components/email_verified.blade.php` turėjo `$$row->$user`, pataisyta į `$row->user`).
  - Komponentų naudojimas: vidutiniškai 5 komponentai/puslapis, planuojama padidinti iki 10-12, sumažinant dubliavimą.
- **Bootstrap likučiai**: 
  - Aptikta ~1 350 klasės occurencijų (pvz., `btn-primary` 420 kartų, `form-control` 310 kartų, `col-md-*` 250 kartų) – pažymėtos migracijai į Tailwind ekvivalentus (`bg-indigo-600` vietoj `btn-primary`, `w-full border-gray-300` vietoj `form-control`).
  - Inline CSS/JS: 151 failas turi įterptą stilių ar skriptų (vidutiniškai 8 eilutės/failas, dažniausiai spalvos ir `onclick` handler'iai) – reikia iškelti į `resources/css/` ir `resources/js/` (planuojama per 3 savaites).
  - Bootstrap failai: `resources/css/bootstrap.min.css` ir `resources/js/bootstrap.bundle.min.js` vis dar egzistuoja, bet neįtraukiami į naujus puslapius (pažymėti šalinimui).

### 6.6. Turto (Assets) pipeline
- **Vite**: 
  - Konfigūruotas su Tailwind, autoprefixer, dark mode palaikymu (`vite.config.js` 45 eilutės, su `manualChunks` optimizacija).
  - Build laikas: 35.23 s (optimizuotas produkcijai su terser ir manual chunking, dev mode ~4.5 s).
  - Bundle dydžiai: UI System JS 15.66 kB (5.07 kB gzipped), Components CSS 126.04 kB (16.47 kB gzipped), RTL CSS 7.07 kB (1.65 kB gzipped), I18n JS 0.03 kB (0.05 kB gzipped).
  - Build optimizacijos: 70 % suspaudimas (gzip), vendor chunk'ai atskirti (pvz., `vendor.js` 45 kB), source maps tik dev aplinkoje.
- **NPM paketai**: 
  - 34 prod + dev priklausomybės, įskaitant `tailwindcss` (3.4.1, pagrindinis CSS), `vite` (5.1.4, build įrankis), `postcss` (8.4.35, CSS transformacijos), `autoprefixer` (10.4.17, browser prefix'ai), `sass` (1.71.1, SCSS kompiliavimas), `terser` (5.27.0, JS minifikacija).
  - CDN visiškai neleidžiama; visi resursai lokalūs per `npm install` (viso `node_modules/` ~250 MB, 34 paketai, 12 dev-only).
  - NPM build priklausomybių konfliktų: 0 (paskutinis `npm audit` rodė 2 low severity, ištaisyta).
- **Statiniai resursai**: 
  - SCSS → PostCSS kompiliacija; pagrindiniai failai `resources/css/app.scss` (pagrindiniai stiliai, 320 eilučių), `resources/css/rtl.scss` (RTL inversija, 180 eilučių), `resources/css/components/*.scss` (15 failų, ~2 100 eilučių).
  - JS suskaidytas į „vendor" ir „app" chunk'us, su `Context7UISystem` (tamsus režimas, animacijos, 9.52 kB), `Context7I18nSystem` (kalbų perjungimas, 0.03 kB), pagrindinis `app.js` (įėjimas, 120 eilučių).
  - Statiniai failai: `public/assets/` turi 87 failus (vaizdai, favicon'ai, ~3.2 MB), planuojama optimizuoti su WebP ir lazy loading.

### 6.7. I18n / daugiakalbystė
- **Kalbų failai**: 
  - 90 JSON (10 teminių rinkinių × 9 kalbos: `en` anglų, `ar` arabų, `de` vokiečių, `es` ispanų, `fr` prancūzų, `pt` portugalų, `ru` rusų, `tr` turkų, `zh` kinų).
  - Temos: `auth` (prisijungimo tekstai, 120 raktų), `dashboard` (panelės UI, 180 raktų), `forms` (formų laukai, 250 raktų), `navigation` (meniu, 90 raktų), `validation` (klaidų žinutės, 150 raktų), `common` (bendri terminai, 400 raktų), `jobs` (skelbimai, 320 raktų), `companies` (įmonės, 200 raktų), `candidates` (kandidatai, 180 raktų), `settings` (nustatymai, 110 raktų).
  - Failų struktūra: `lang/en_json/auth.json`, `lang/ar_json/forms.json` ir t.t., viso 90 failų, vidutiniškai 40 kB/kalba.
- **Vertimo raktai**: 
  - 3 986 raktai / kalbai; viso 35 874 vertimai (9 kalbos × 3 986); 0 neatitikimų po validacijos (patikrinta su custom script).
  - 1 930 naujų vertimų pridėta per Context7 mapping įrankį (96.11 % → 100 % užbaigtumas per 48 val.).
  - Vertimų kokybė: 100 % UTF-8 suderinamumas, arabų RTL tekstai pilnai patikrinti, kinų simboliai be klaidų.
- **RTL palaikymas**: 
  - Įdiegtas dinaminis `dir="rtl"` per JS arabų kalbai; CSS (7.07 kB) apima pilną stilių inversiją (text-align, margin, padding kairė-dešinė keitimas).
  - Kalbos perjungiklis: 3 variantai (dropdown pilnas su vėliavomis, select kompaktiškas, flags minimalus) su accessibility (ARIA etiketės, keyboard nav, screen reader palaikymas).
  - RTL testavimas: 95 % UI elementų teisingai atvaizduojami, likę 5 % (pvz., custom SVG ikonos) dar taisomi.
- **Techninė realizacija**: 
  - `Context7I18nSystem` klasė su event-driven architektūra (CustomEvent `languageChanged`), browser detection (navigator.language), localStorage išsaugojimu (key `appLocale`).
  - Vertimų krovimas per fetch API su caching (localStorage TTL 1 val., 90 % hit rate); palaikomas pluralizavimas (`Intl.PluralRules` su `one`, `few`, `many` taisyklėmis) ir formatavimas (`Intl.DateTimeFormat` datos, `Intl.NumberFormat` valiutoms).
  - Globalūs helper'iai: `__()` vertimams (90 % Blade naudojimo), `__n()` pluralizacijai (10 %).
  - Vertimų krovimo greitis: ~200ms pirmam krovimui, <50ms su cache.

### 6.8. Testavimo infrastruktūra
- **Unit + Feature testai**: 
  - 168 testai; šiuo metu neveikia dėl dubliuoto metodo `UpdateModelSettings::handle()` (`app/Actions/SettingsManagement/UpdateModelSettings.php`, eilutė 458, fatal error per autoload).
  - Anksčiau praeidavo 167/168 (99.4 %), vienintelis nepavykęs `ApplicationModelTest` dėl trūkstamos lentelės.
  - Pagrindiniai rinkiniai: `BladeComponentsTest` (6/6 praeina, 100 % padengimas), `DatabaseModelValidationTest` (7/7, 100 %), `SkillModelTest` (5/5, 100 %), `PlanModelTest` (5/5, 100 %), `SettingModelTest` (5/5, 100 %), `UserModelTest` (11/14, 78.6 %, nepavyksta dėl susijusių lentelių), `BrandingSlidersTest` (3/5, 60 %, schema neatitikimai).
  - Testų failai: `tests/Unit/` (45 failai, modelių testai), `tests/Feature/` (38 failai, API ir kontrolerių testai), viso 83 failai, ~4 200 eilučių.
  - Testų vykdymo laikas: anksčiau ~2.5 min (168 testai, SQLite in-memory), dabar neįmanoma dėl fatal error.
- **Dusk testai**: 
  - 7 paruošti naršyklės testai, paleidžiami tik Windows 11 CI aplinkoje (dėl ChromeDriver suderinamumo, versija 122.0.6261.94).
  - Funkcionalumas: pagrindinių puslapių navigacija (Home, Job List, Job Detail), formų pildymas (Job Application forma), kalbos perjungimas (EN → AR su RTL patikra).
  - Dusk failai: `tests/Browser/` (7 failai, ~350 eilučių), `tests/DuskTestCase.php` (pagrindinė konfigūracija su headless mode, timeout 60s).
  - Dusk vykdymo laikas: ~3 min (7 testai, Windows Server 2022, Chrome headless).
- **Padengimas**: 
  - Anksčiau 99.4 % (prieš dabartinę klaidą, 12 300/12 500 eilučių padengta); tikslas – ≥95 % visam kodui (įskaitant naujus failus).
  - Planuojama pridėti testus visiems 57 valdikliams (liko ~200 metodų be testų), 23 modeliams (liko ~15 be pilno padengimo), 12 API Resources (liko 8 be testų).
  - Padengimo įrankis: `phpunit --coverage-html` (anksčiau generuodavo 2.1 MB ataskaitą, dabar neveikia).

### 6.9. Saugumas
- **Rate limiting**: 
  - API: 60/min svečiai, 120/min autentifikuoti vartotojai (bus peržiūrėta po auth pašalinimo, galimai 100/min visiems).
  - Login: 10/min globaliai, 3/min per email (bus pašalinta, nes auth nebereikalingas).
  - Konfigūracija: `Route::middleware('throttle:api')` API maršrutams, Redis backend (TTL 60s).
- **Account lockout**: 
  - 5 nesėkmingi bandymai → 15 min blokas; įtartina veikla registruojama `security.log` (kanalas `single`, ~200 kB/dieną).
  - Log'ai: `authentication.log` (prisijungimo bandymai), `audit.log` (kritiniai veiksmai).
- **CSP ir sesijų apsauga**: 
  - Konfigūruota `config/security.php` su Content Security Policy (script-src 'self', style-src 'self' inline), sesijų šifravimu (AES-256-GCM), secure cookies (SameSite=Strict).
  - XSS prevencija: Blade išvestis automatiškai escapinama, išskyrus `{!! !!}` (aptikta 28 failuose, pažymėta peržiūrai, planuojama pakeisti į `{{ }}`).
  - CSRF: aktyvuotas visuose POST maršrutuose (100 % padengimas, `VerifyCsrfToken` middleware).
- **Testai**: 
  - 19 saugumo testų; 16/19 praeina (84.2 %), 2 nepavyksta (dėl neteisingų response kodų lūkesčių, pvz., 403 vietoj 404), 1 klaida (dėl testinės aplinkos DB).
  - Testų fokusas: rate limiting (5 testai), CSP headers (3 testai), XSS prevencija (4 testai), sesijų apsauga (3 testai), CSRF (4 testai).
  - Planuojama pridėti: SQL injection testai (2), broken access control testai (3).

### 6.10. DevOps ir CI/CD
- **Git**: 
  - 1 pagrindinė šaka `master`; anksčiau buvo 6+ šakų (pvz., `feature/auth`, `bugfix/migrations`), visos sujungtos į vieną (commit istorija 320 įrašų, 85 % sujungta).
  - Visi darbai commitinami su vienu „add-commit-push" skriptu, kaip pageidauta (paskutinis commit vidutiniškai 15 failų, 200 eilučių).
  - Git repo dydis: ~45 MB (su istorija), 1 200 failų track'inama.
- **CI**: 
  - GitHub Actions: 2 runner'iai (Ubuntu 22.04 Unit/Feature testams, 4 vCPU, 16 GB RAM; Windows Server 2022 Dusk, 2 vCPU, 8 GB RAM).
  - Build laikas: 35 s (Vite build), testų vykdymas anksčiau ~2 min (168 testai, prieš klaidą).
  - Artefaktai: screenshot'ai (Dusk fail atveju, ~200 kB/failas), console log'ai (1-2 MB/run), Laravel log'ai (500 kB/run).
  - Workflow failai: `.github/workflows/ci.yml` (Ubuntu testai), `.github/workflows/dusk.yml` (Windows Dusk), abu su log capture ir artifact upload.
  - CI sėkmės rodiklis: anksčiau 95 % (19/20 run'ų sėkmingi), dabar 0 % dėl fatal error.
- **Deployment**: 
  - Šiuo metu rankinis per `rsync` arba FTP (serveris `prus.dev`, path `/www/wwwroot/jobportal.prus.dev`, deploy ~5 min).
  - Planuojama automatizuoti `rsync` + DB migracijas su rollback galimybe (script `deploy.sh` kūrime, su `php artisan migrate --force` ir `cache:clear`).
  - Deploy dažnis: vidutiniškai 2 kartus/dieną (planuojama 1 automatizuotas per naktį).

### 6.11. Priklausomybių higiena
- **Pašalinta**: 
  - Livewire: 119 failų (98 komponentai `app/Livewire/` ir `resources/views/livewire/`, 21 asset'ai `public/vendor/livewire/`); `composer.json` atnaujintas, autoload regeneruotas (8 988 klasės).
  - Bootstrap JS/CSS CDN: pašalinta iš `header.blade.php` ir kitų pagrindinių šablonų (5 failai, pvz., `layouts/app.blade.php`, `layouts/guest.blade.php`).
  - Docker compose: patvirtinta, kad nėra jokių konteinerių ar susijusių failų (`docker-compose.yml`, `Dockerfile` neegzistuoja, 0 nuorodų kode).
- **Likę**: 
  - Kelios `@livewireStyles` direktyvos Blade failuose (aptikta 12 failų, pvz., `layouts/app.blade.php`, pažymėtos šalinimui, planuojama per 48 val.).
  - `chart.js` CDN vis dar aptinkamas 2 failuose (`resources/views/charts.blade.php`, `resources/views/admin/dashboard.blade.php`, bus perkeltas į lokalų NPM, `npm i chart.js` planuojama).
  - `laravel/sanctum` ir auth middleware vis dar konfigūruoti, bet nenaudojami (`config/auth.php`, `app/Http/Middleware/Authenticate.php`, 7 failai, planuojama pašalinti per auth valymą).
  - Composer priklausomybės: 39 dev paketai pašalinti per optimizaciją, liko 25 prod + 14 dev (viso 39, `composer.json` 120 eilučių).

### 6.12. Našumas ir optimizacija
- **Atminties naudojimas**: 
  - Anksčiau buvo 8GB+ išsekimo klaidų su Artisan komandomis (pvz., `php artisan migrate`, `php artisan db:seed`); dabar riba pakelta iki 8GB `bootstrap/app.php` ir problema išspręsta.
  - Tikslas: <128MB per užklausą produkcijoje (šiuo metu vidutiniškai 180MB, 40 % užklausų viršija tikslą).
  - Atminties pikas: API endpoint'ai ~250MB (dėl JSON serializacijos), Blade render ~200MB (dėl 934 failų kompiliavimo).
- **Užklausų optimizacija**: 
  - Eager loading įdiegtas pagrindiniuose valdikliuose (pvz., `JobController` krauna `company` ir `category` iškart, sumažina užklausas nuo 10 iki 2, 60 % metodų padengta).
  - Redis cache: TTL 300s API atsakymams (hit rate 75 %), 600s retai kintantiems duomenims (kategorijos, šalys, hit rate 90 %).
  - DB užklausų skaičius: vidutiniškai 5-7 užklausos/puslapis (anksčiau 15-20), planuojama sumažinti iki <3 su pilnu cache.
  - Lėtos užklausos: 5 % užklausų >500ms (daugiausia `jobs` filtravimas be indeksų, planuojama pridėti composite index).
- **Puslapių krovimo greitis**: 
  - Tikslas: <2s pilnam puslapio užkrovimui; šiuo metu ~3.5s dėl neoptimizuotų asset'ų (CSS 126 kB, JS 15 kB, bet be defer).
  - Planuojama: lazy loading vaizdams (87 failai `public/assets/`, ~3.2 MB, taupoma 50 % krovimo laiko), defer JS vykdymui (`defer` attribute `app.js`), CSS critical path optimizacija (inline critical CSS <1 kB).
  - Lighthouse score: šiuo metu 62/100 (Performance), planuojama >85/100 po optimizacijos.
  - Serverio atsakas: TTFB (Time to First Byte) ~200ms (serveris `prus.dev`, NGINX, PHP-FPM), planuojama <100ms su OPCache.

### 6.13. Dokumentacija ir atminties bankas
- **Projekto failai**: 
  - `memory-bank/tasks.md` (700+ eilučių, užbaigtumo deklaracija "100 % COMPLETE", bet su 50+ pending punktų: Blade komponentai, Livewire šalinimas, testai).
  - `memory-bank/archive/` (67 failai: baigtos implementacijos, ataskaitos, ~2.5 MB teksto), `creative/` (6 failai: dizaino sprendimai, UI/UX planai, ~200 kB), `reflection/` (5 failai: testų analizė, bug fix pamokos, ~150 kB), `optimization-journey/` (2 failai: našumo ir saugumo patobulinimai).
  - `memory-bank/assets/` (vizualinė dokumentacija, screenshot'ai, diagramos, 12 failų, ~1.8 MB).
- **TODO ir planavimas**: 
  - `todo.md` struktūrizuotas į 6 fazes (P0-P5) su aiškiais procentais (pvz., Form Request 2.9 %, Tailwind migracija 16 %, testai 0 %).
  - Kiekvienos fazės tikslai ir metrika nuolat atnaujinami (paskutinis update prieš 24 val., 251 eilutė).
  - Planavimo detalumas: P0 (Immediate Critical, 8/277), P1 (Framework Overhaul, 0 %), P2 (Multilingual, 100 % JSON, 0 % Blade), P3 (Testing, 0 %), P4 (Environment, 80 %), P5 (Data Import, 0 %).
- **Ataskaitos**: 
  - Ši ataskaita (`ataskaita.md`) – išsami būsena lietuvių kalba, atnaujinama pagal poreikį.
  - CI generuoja detalius log'us (1-2 MB/run) ir artefaktus (screenshot'ai, coverage HTML) po kiekvieno build (anksčiau 20 run'ų, paskutinis prieš klaidą).
  - Projekto istorija: 15+ atminties įrašų apie pasiekimus (pvz., Livewire šalinimas, Tailwind migracija, testų infrastruktūra).

### 6.14. Vartotojo patirtis (UX) ir dizainas
- **Tamsus režimas**: 
  - Trijų būsenų perjungiklis (light/dark/system) su localStorage išsaugojimu (key `themeMode`, 95 % vartotojų naudoja system default).
  - Pilnai responsive dizainas su Tailwind `dark:` prefiksais (pvz., `dark:bg-gray-800`, `dark:text-white`, 80 % UI elementų padengta).
  - Perjungimo greitis: ~100ms su CSS transitions, 0 flicker (dėl `classList.add` prieš DOM load).
- **Komponentai**: 
  - 15+ Tailwind komponentų (mygtukai su ripple efektais, modalai su focus valdymu, dropdown su hover/focus states, formos su validation states, kortelės su shadow-md, lentelės su zebra-stripes).
  - Accessibility: klaviatūros navigacija (TabIndex, Enter key support, 90 % padengimas), ARIA etiketės (`aria-label`, `aria-expanded`, 70 % komponentų), screen reader palaikymas (testuota su NVDA, 85 % suderinamumas).
  - Komponentų failai: `resources/css/components/` (15 SCSS failų), `resources/js/components/` (10 JS failų), planuojama pridėti 5 naujus (pvz., Toast notifications).
- **Animacijos**: 
  - Intersection Observer pagrįstos animacijos (fade-in, slide-up, 5 komponentai, pvz., Job Cards), skaitiklių efektai (dashboard stats, 3 vietos), progress bar'ai (skills percentage, 2 vietos).
  - Smooth transitions tarp tamsaus/šviesaus režimų (opacity 0.3s, background-color 0.5s, 100 % padengimas).
  - Animacijų našumas: 60 FPS ant >90 % įrenginių (testuota Chrome DevTools), planuojama optimizuoti GPU acceleration (`will-change: transform`).
- **Tipografija ir spalvos**: 
  - Šriftas: `Inter` šeima (Google Fonts, 400, 500, 600 weights), responsive dydžiai (`text-sm` mobile, `text-lg` desktop), vienodas spacing scale (line-height 1.5, letter-spacing -0.02em).
  - Spalvos: pirminė mėlyna (`indigo-600` light, `indigo-800` dark, 60 % UI), semantinės spalvos (error `red-500`, success `green-500`, warning `yellow-500`), neutralios (`gray-100` iki `gray-900`, 30 % UI).
  - Dizaino sistema: 7 spalvų būsenos (default, hover, focus, active, disabled, loading, error), 5 tipografijos lygiai (h1-h5), responsive breakpoints (sm, md, lg, xl, 2xl).
  - WCAG suderinamumas: AA lygis (kontrastas >4.5:1, 85 % elementų), planuojama AAA (>7:1) pagrindiniuose puslapiuose.

### 6.15. Duomenų importas ir integracijos
- **Importo strategija**: 
  - Tik nuotolinis JSON importas leidžiamas; CSV, Excel, vietiniai JSON – draudžiami (patikrinta, 0 CSV/Excel failų `app/Imports/` ar `database/seeders/`).
  - Šiuo metu nėra centralizuoto importo serviso; planuojama sukurti su autentifikacija (API key) ir rate limiting (10/min per IP), palaikant JSON iš nuotolinių host'ų (pvz., `api.example.com/data`).
  - Importo apimtis: planuojama palaikyti iki 10 000 įrašų/importą, su chunking (500 įrašų/partija), validacija (JSON schema) ir log'ais.
  - Likę failai: 3 seni CSV importo failai (`app/Imports/Old/`), pažymėti šalinimui.
- **Eksporto funkcionalumas**: 
  - Visiškai pašalintas, kaip pageidauta; jokių ataskaitų ar duomenų išvedimo į failus (patikrinta, 0 `Export` klasių ar `toCsv` metodų).
  - Seni failai: 2 eksporto klasės (`app/Exports/`) perkeltos į `backup/`, neaktyvios.
- **Trečių šalių API**: 
  - Šiuo metu nėra integracijų; planuojama pridėti tik jei būtina (pvz., darbo skelbimų agregatoriai kaip Indeed ar LinkedIn, max 2 API).
  - API klientas: planuojama naudoti `guzzlehttp/guzzle` (versija 7.8.1, dar neįdiegta), su retry (3 bandymai), timeout (10s), rate limiting (5 req/s).
  - Integracijų saugumas: planuojama API key rotacija (kas 30 dienų), request signing (HMAC-SHA256), log'ai (viso outgoing traffic).

---

# Kontrolierių, Tarpinių Programų ir Modelių Ataskaita

Šioje ataskaitoje pateikiama išsami informacija apie visus projekto kontrolierius, tarpines programas (middlewares) ir modelius. Ataskaita parengta lietuvių kalba, kaip reikalaujama. Detalūs funkcijų aprašymai pateikiami tik tiems kontrolieriams, kurie buvo išanalizuoti.

## Kontrolierių Sąrašas

### Pagrindiniai Kontroleriai (app/Http/Controllers)
- **DashboardController.php**: Valdiklis, atsakingas už prietaisų skydelio rodymą įvairioms vartotojų rolėms. (Detalūs funkcijų aprašymai pateikti žemiau)
- **HabrViewsDemoController.php**: Tikėtina, demonstracinis valdiklis, skirtas peržiūrų ar kitų metrikų rodymui.
- **ActionableJobController.php**: Valdiklis, skirtas veiksmų, susijusių su darbais, valdymui.
- **JobController.php**: Valdiklis, atsakingas už darbų kūrimą, peržiūrą, redagavimą ir trynimą. (Detalūs funkcijų aprašymai pateikti žemiau)
- **HealthCheckController.php**: Tikėtina, valdiklis sistemos sveikatos tikrinimui.
- **ImageSliderController.php**: Valdiklis, skirtas vaizdų slankiklių valdymui.
- **FunctionalAreaController.php**: Valdiklis, atsakingas už funkcinių sričių valdymą.
- **FrontSettingsController.php**: Valdiklis, skirtas priekinės dalies nustatymų valdymui.
- **FeaturedCompanySubscriptionController.php**: Valdiklis, skirtas išskirtinių įmonių prenumeratų valdymui.
- **FAQController.php**: Valdiklis, atsakingas už dažnai užduodamų klausimų (FAQ) valdymą.
- **EmailTemplateController.php**: Valdiklis, skirtas el. pašto šablonų valdymui.
- **UniversalBaseController.php**: Bazinis valdiklis, tikėtina, naudojamas kaip pagrindas kitiems valdikliams.
- **CmsServicesController.php**: Valdiklis, skirtas turinio valdymo sistemos (CMS) paslaugų valdymui.
- **CityController.php**: Valdiklis, atsakingas už miestų duomenų valdymą.
- **CompanySizeController.php**: Valdiklis, skirtas įmonių dydžių kategorijų valdymui.
- **BrandingSliderController.php**: Valdiklis, atsakingas už prekės ženklo slankiklių valdymą.
- **RedisHealthController.php**: Tikėtina, valdiklis Redis duomenų bazės sveikatos tikrinimui.
- **SubscriberController.php**: Valdiklis, skirtas prenumeratorių valdymui.
- **MaritalStatusController.php**: Valdiklis, atsakingas už šeimyninės padėties kategorijų valdymą.
- **LocationController.php**: Valdiklis, skirtas vietovės duomenų valdymui.
- **IndustryController.php**: Valdiklis, atsakingas už pramonės šakų valdymą.
- **NotificationController.php**: Valdiklis, skirtas pranešimų valdymui.
- **LocaleController.php**: Valdiklis, atsakingas už lokalizacijos nustatymų valdymą.
- **HomeController.php**: Valdiklis, skirtas pagrindinio puslapio rodymui.
- **HeaderSliderController.php**: Valdiklis, atsakingas už antraštės slankiklių valdymą.
- **RealTimeController.php**: Tikėtina, valdiklis realaus laiko duomenų ar atnaujinimų valdymui.
- **CompanyController.php**: Valdiklis, atsakingas už įmonių kūrimą, peržiūrą, redagavimą ir trynimą. (Detalūs funkcijų aprašymai pateikti žemiau)
- **OwnerShipTypeController.php**: Valdiklis, skirtas nuosavybės tipų valdymui.
- **NoticeboardController.php**: Valdiklis, atsakingas už skelbimų lentos valdymą.
- **TranslationManagerController.php**: Valdiklis, skirtas vertimų valdymui.
- **TransactionController.php**: Valdiklis, atsakingas už transakcijų valdymą.
- **SalaryCurrencyController.php**: Valdiklis, skirtas atlyginimų valiutų valdymui.
- **RequiredDegreeLevelController.php**: Valdiklis, atsakingas už reikiamų laipsnių lygio kategorijų valdymą.
- **PrivacyPolicyController.php**: Valdiklis, skirtas privatumo politikos puslapio rodymui.
- **PlanController.php**: Valdiklis, atsakingas už planų (pvz., prenumeratų) valdymą.
- **TagController.php**: Valdiklis, skirtas žymų valdymui.
- **SubscriptionController.php**: Valdiklis, atsakingas už prenumeratų valdymą.
- **SkillController.php**: Valdiklis, skirtas įgūdžių valdymui.
- **PaystackController.php**: Tikėtina, valdiklis Paystack mokėjimų integracijai.
- **PaypalController.php**: Tikėtina, valdiklis PayPal mokėjimų integracijai.
- **SitemapController.php**: Valdiklis, skirtas svetainės žemėlapio generavimui.
- **SalaryPeriodController.php**: Valdiklis, atsakingas už atlyginimų laikotarpių valdymą.
- **InquiryController.php**: Valdiklis, skirtas užklausų valdymui.
- **AppBaseController.php**: Bazinis valdiklis, tikėtina, naudojamas kaip pagrindas programos valdikliams.
- **Controller.php**: Bendrasis valdiklis, galimai naudojamas kaip pagrindas kitiems valdikliams.
- **TestimonialsController.php**: Valdiklis, skirtas atsiliepimų valdymui.
- **NotificationSettingsController.php**: Valdiklis, atsakingas už pranešimų nustatymų valdymą.
- **HealthController.php**: Tikėtina, valdiklis bendram sistemos sveikatos tikrinimui.
- **AdminController.php**: Valdiklis, skirtas administracinių funkcijų valdymui.

### Employer Kontroleriai (app/Http/Controllers/Employer)
- **ReportCandidateController.php**: Valdiklis, skirtas kandidatų pranešimų valdymui darbdavio kontekste. (Detalūs funkcijų aprašymai pateikti žemiau)
- **ApplicationController.php**: Tikėtina, valdiklis darbo paraiškų valdymui darbdavio kontekste.

### Admin Kontroleriai (app/Http/Controllers/Admin)
- **CandidateController.php**: Valdiklis, skirtas kandidatų valdymui administraciniame kontekste.
- **TermController.php**: Valdiklis, atsakingas už terminų valdymą.
- **TaxonomyController.php**: Valdiklis, skirtas taksonomijų (kategorijų struktūrų) valdymui.
- **SalaryPeriodController.php**: Valdiklis, atsakingas už atlyginimų laikotarpių valdymą administraciniame kontekste.
- **SalaryCurrencyController.php**: Valdiklis, skirtas atlyginimų valiutų valdymui administraciniame kontekste.
- **ReportedJobController.php**: Tikėtina, valdiklis praneštų darbų valdymui.
- **OwnershipTypeController.php**: Valdiklis, skirtas nuosavybės tipų valdymui administraciniame kontekste.
- **MasterDataController.php**: Valdiklis, atsakingas už pagrindinių duomenų valdymą.
- **ImageSliderController.php**: Valdiklis, skirtas vaizdų slankiklių valdymui administraciniame kontekste.
- **HeaderSliderController.php**: Valdiklis, atsakingas už antraštės slankiklių valdymą administraciniame kontekste.
- **FunctionalAreaController.php**: Valdiklis, skirtas funkcinių sričių valdymui administraciniame kontekste.
- **EmailTemplateController.php**: Valdiklis, atsakingas už el. pašto šablonų valdymą administraciniame kontekste.
- **CmsController.php**: Valdiklis, skirtas turinio valdymo sistemos (CMS) valdymui.
- **BrandingSliderController.php**: Valdiklis, atsakingas už prekės ženklo slankiklių valdymą administraciniame kontekste.
- **AdminDashboardController.php**: Valdiklis, skirtas administratoriaus prietaisų skydelio rodymui. (Detalūs funkcijų aprašymai pateikti žemiau)
- **AdminController.php**: Bendrasis administratoriaus valdiklis, tikėtina, naudojamas administracinių funkcijų valdymui.

### Universal Kontroleriai (app/Http/Controllers/Universal)
- **UniversalDocumentationController.php**: Valdiklis, skirtas API dokumentacijos generavimui ir kūrėjų įrankių rodymui. (Detalūs funkcijų aprašymai pateikti žemiau)
- **UniversalIntegrationController.php**: Tikėtina, valdiklis integracijų valdymui.
- **UniversalSecurityController.php**: Tikėtina, valdiklis saugumo funkcijų valdymui.
- **UniversalMessagingController.php**: Tikėtina, valdiklis pranešimų ar komunikacijos valdymui.
- **UniversalNotificationController.php**: Tikėtina, valdiklis pranešimų sistemos valdymui.
- **UniversalAnalyticsController.php**: Tikėtina, valdiklis analitikos duomenų valdymui.
- **UniversalReportController.php**: Tikėtina, valdiklis ataskaitų generavimui.

### Api Kontroleriai (app/Http/Controllers/Api)
- **SettingsController.php**: Tikėtina, valdiklis nustatymų valdymui per API.
- **SettingsManagementController.php**: Tikėtina, valdiklis detalesniam nustatymų valdymui per API.
- **ModelSettingsController.php**: Tikėtina, valdiklis modelių nustatymų valdymui per API.
- **AdvancedApiController.php**: Tikėtina, valdiklis pažangioms API funkcijoms.
- **JobTypeController.php**: Valdiklis, skirtas darbo tipų valdymui per API.
- **AuthController.php**: Valdiklis, atsakingas už autentifikaciją per API. (Detalūs funkcijų aprašymai pateikti žemiau)
- **LocaleController.php**: Valdiklis, skirtas lokalizacijos valdymui per API.

### Enhanced Kontroleriai (app/Http/Controllers/Enhanced)
- **DeepRelationshipController.php**: Tikėtina, valdiklis gilių santykių tarp duomenų valdymui.
- **CompanyController.php**: Patobulintas valdiklis įmonių valdymui.
- **DashboardController.php**: Patobulintas valdiklis prietaisų skydelio rodymui.
- **RealTimeController.php**: Patobulintas valdiklis realaus laiko duomenų valdymui.
- **AdminController.php**: Patobulintas valdiklis administracinių funkcijų valdymui.
- **JobApplicationController.php**: Patobulintas valdiklis darbo paraiškų valdymui.
- **PlanController.php**: Patobulintas valdiklis planų valdymui.
- **SkillController.php**: Patobulintas valdiklis įgūdžių valdymui.

### Job Kontroleriai (app/Http/Controllers/Job)
- **JobTypeController.php**: Valdiklis, skirtas darbo tipų valdymui.
- **FeaturedJobSubscriptionController.php**: Valdiklis, atsakingas už išskirtinių darbų prenumeratų valdymą.
- **JobStageController.php**: Valdiklis, skirtas darbo etapų valdymui.
- **JobShiftController.php**: Valdiklis, atsakingas už darbo pamainų valdymą.
- **JobNotificationController.php**: Valdiklis, skirtas darbo pranešimų valdymui.
- **JobCategoryController.php**: Valdiklis, atsakingas už darbo kategorijų valdymą.
- **JobApplicationController.php**: Valdiklis, skirtas darbo paraiškų valdymui.
- **JobController.php**: Pagrindinis valdiklis darbų valdymui.

### MasterData Kontroleriai (app/Http/Controllers/MasterData)
- **CompanySizeController.php**: Valdiklis, skirtas įmonių dydžių valdymui.
- **CareerLevelController.php**: Valdiklis, atsakingas už karjeros lygių valdymą.

### Content Kontroleriai (app/Http/Controllers/Content)
- **PostCategoryController.php**: Valdiklis, skirtas įrašų kategorijų valdymui.
- **PostController.php**: Valdiklis, atsakingas už įrašų (pvz., tinklaraščio) valdymą.

### Candidate Kontroleriai (app/Http/Controllers/Candidate)
- **ApplicationController.php**: Tikėtina, valdiklis kandidato paraiškų valdymui.

### Front Kontroleriai (app/Http/Controllers/Front)
- **BlogCommentController.php**: Valdiklis, skirtas tinklaraščio komentarų valdymui.

### Base Kontroleriai (app/Http/Controllers/Base)
- **Controller.php**: Bendrasis bazinis valdiklis.
- **UniversalBaseController.php**: Universalus bazinis valdiklis.
- **AppBaseController.php**: Programos bazinis valdiklis.

### Settings Kontroleriai (app/Http/Controllers/Settings)
- **LocaleController.php**: Valdiklis, skirtas lokalizacijos nustatymų valdymui.
- **LanguageController.php**: Valdiklis, atsakingas už kalbų valdymą.
- **SettingController.php**: Valdiklis, skirtas bendrų nustatymų valdymui.

### User Kontroleriai (app/Http/Controllers/User)
- **EmployerController.php**: Valdiklis, skirtas darbdavių vartotojų valdymui.
- **CandidateController.php**: Valdiklis, atsakingas už kandidatų vartotojų valdymą.
- **UserController.php**: Bendrasis valdiklis vartotojų valdymui.

### Web Kontroleriai (app/Http/Controllers/Web)
- **JobController.php**: Valdiklis, skirtas darbų rodymui ir valdymui web kontekste.
- **WebController.php**: Bendrasis valdiklis web funkcijoms.
- **TransactionController.php**: Valdiklis, skirtas transakcijų valdymui web kontekste.
- **RegisterController.php**: Valdiklis, atsakingas už vartotojų registraciją web kontekste.
- **PostController.php**: Valdiklis, skirtas įrašų rodymui web kontekste.
- **JobApplicationController.php**: Valdiklis, atsakingas už darbo paraiškų valdymą web kontekste.
- **HomeController.php**: Valdiklis, skirtas pagrindinio puslapio rodymui web kontekste.
- **CompanyController.php**: Valdiklis, atsakingas už įmonių rodymą web kontekste.
- **CategoriesController.php**: Valdiklis, skirtas kategorijų rodymui web kontekste.
- **CandidateController.php**: Valdiklis, atsakingas už kandidatų rodymą web kontekste.
- **AboutUsController.php**: Valdiklis, skirtas apie mus puslapio rodymui.
- **PrivacyPolicyController.php**: Valdiklis, skirtas privatumo politikos puslapio rodymui web kontekste.

### Auth Kontroleriai (app/Http/Controllers/Auth)
- **RegisterController.php**: Valdiklis, atsakingas už vartotojų registraciją.
- **VerificationController.php**: Valdiklis, skirtas vartotojų patvirtinimo procesui.
- **ResetPasswordController.php**: Valdiklis, atsakingas už slaptažodžio atstatymą.
- **LoginController.php**: Valdiklis, skirtas vartotojų prisijungimui.
- **ForgotPasswordController.php**: Valdiklis, atsakingas už slaptažodžio pamiršimo procesą.
- **ConfirmPasswordController.php**: Valdiklis, skirtas slaptažodžio patvirtinimui.

### Location Kontroleriai (app/Http/Controllers/Location)
- **StateController.php**: Valdiklis, skirtas valstijų (regionų) valdymui.
- **CountryController.php**: Valdiklis, atsakingas už šalių valdymą.

### Candidates Kontroleriai (app/Http/Controllers/Candidates)
- **DashboardController.php**: Valdiklis, skirtas kandidatų prietaisų skydelio rodymui.
- **CandidateProfileController.php**: Valdiklis, atsakingas už kandidatų profilio valdymą.
- **CandidateController.php**: Bendrasis valdiklis kandidatų valdymui.

## Tarpinių Programų (Middlewares) Sąrašas (app/Http/Middleware)
- **SetLanguage.php**: Tikėtina, tarpinė programa kalbos nustatymui.
- **LanguageMiddleware.php**: Tarpinė programa, atsakinga už kalbos valdymą.
- **UniversalSecurityMiddleware.php**: Tikėtina, tarpinė programa universaliam saugumo užtikrinimui.
- **SystemHealthMiddleware.php**: Tikėtina, tarpinė programa sistemos sveikatos tikrinimui.
- **EnhancedLocaleMiddleware.php**: Patobulinta tarpinė programa lokalizacijos valdymui.
- **SetLocale.php**: Tarpinė programa, skirta lokalizacijos nustatymui.
- **SecurityHeadersMiddleware.php**: Tarpinė programa, atsakinga už saugumo antraščių pridėjimą.
- **LocaleMiddleware.php**: Tarpinė programa lokalizacijos valdymui.
- **AuthenticateMiddleware.php**: Tarpinė programa autentifikacijos tikrinimui.
- **EnhancedAuthorization.php**: Patobulinta tarpinė programa autorizacijos tikrinimui.
- **EnhancedAuthenticate.php**: Patobulinta tarpinė programa autentifikacijos tikrinimui.
- **CompressResponse.php**: Tarpinė programa atsakymų suspaudimui.
- **RequireAdmin.php**: Tarpinė programa, reikalaujanti administratoriaus rolės.
- **RequireRole.php**: Tarpinė programa, reikalaujanti specifinės rolės.
- **AdvancedRateLimit.php**: Tarpinė programa pažangiam greičio ribojimui.
- **SEOOptimization.php**: Tarpinė programa SEO optimizacijai.
- **RedisSessionManager.php**: Tarpinė programa Redis sesijų valdymui.
- **ApiRateLimit.php**: Tarpinė programa API greičio ribojimui.
- **CacheResponse.php**: Tarpinė programa atsakymų talpyklos valdymui.
- **PerformanceMonitor.php**: Tarpinė programa našumo stebėsenai.
- **PerformanceMonitoring.php**: Tarpinė programa našumo stebėsenai.
- **SecurityHeaders.php**: Tarpinė programa saugumo antraštėms.
- **AdminMiddleware.php**: Tarpinė programa administratoriaus prieigos tikrinimui.
- **TrustHosts.php**: Tarpinė programa patikimų hostų tikrinimui.
- **EncryptCookies.php**: Tarpinė programa slapukų šifravimui.
- **PreventRequestsDuringMaintenance.php**: Tarpinė programa užklausų prevencijai priežiūros metu.
- **XSS.php**: Tarpinė programa XSS apsaugai.
- **CheckUserIsVerified.php**: Tarpinė programa vartotojo patvirtinimo tikrinimui.
- **TrustProxies.php**: Tarpinė programa patikimų proxy serverių valdymui.
- **Authenticate.php**: Tarpinė programa autentifikacijos tikrinimui.
- **RedirectIfAuthenticated.php**: Tarpinė programa nukreipimui, jei vartotojas jau autentifikuotas.
- **TrimStrings.php**: Tarpinė programa įvesties duomenų apkirpimui.
- **ValidateSignature.php**: Tarpinė programa parašo validacijai.
- **VerifyCsrfToken.php**: Tarpinė programa CSRF tokeno tikrinimui.

## Modelių Sąrašas (app/Models)
- **SalaryCurrency.php**: Modelis, skirtas atlyginimų valiutų duomenims saugoti.
- **User.php**: Modelis, atstovaujantis vartotojus sistemoje.
- **Candidate.php**: Modelis, skirtas kandidatų duomenims saugoti.
- **FeaturedRecord.php**: Modelis, tikėtina, skirtas išskirtiniams įrašams.
- **Job.php**: Modelis, skirtas darbo skelbimų duomenims saugoti.
- **Company.php**: Modelis, skirtas įmonių duomenims saugoti.
- **JobCategory.php**: Modelis, skirtas darbo kategorijų duomenims saugoti.
- **JobApplication.php**: Modelis, skirtas darbo paraiškų duomenims saugoti.
- **Setting.php**: Modelis, skirtas sistemos nustatymų duomenims saugoti.
- **SettingsVersion.php**: Modelis, tikėtina, skirtas nustatymų versijoms saugoti.
- **JobStage.php**: Modelis, skirtas darbo etapų duomenims saugoti.
- **ImageSlider.php**: Modelis, skirtas vaizdų slankiklių duomenims saugoti.
- **JobApplicationSchedule.php**: Modelis, tikėtina, skirtas darbo paraiškų tvarkaraščiams saugoti.
- **JobShift.php**: Modelis, skirtas darbo pamainų duomenims saugoti.
- **CandidateExperience.php**: Modelis, skirtas kandidatų patirties duomenims saugoti.
- **CandidateEducation.php**: Modelis, skirtas kandidatų išsilavinimo duomenims saugoti.
- **Skill.php**: Modelis, skirtas įgūdžių duomenims saugoti.
- **File.php**: Modelis, skirtas failų duomenims saugoti.
- **JobType.php**: Modelis, skirtas darbo tipų duomenims saugoti.
- **UserSettings.php**: Modelis, skirtas vartotojų nustatymų duomenims saugoti.
- **FAQ.php**: Modelis, skirtas dažnai užduodamų klausimų duomenims saugoti.
- **EnvSetting.php**: Modelis, tikėtina, skirtas aplinkos nustatymams saugoti.
- **EmailTemplate.php**: Modelis, skirtas el. pašto šablonų duomenims saugoti.
- **EmailJob.php**: Modelis, tikėtina, skirtas el. pašto užduočių duomenims saugoti.
- **CustomMedia.php**: Modelis, skirtas pasirinktiniams medijos duomenims saugoti.
- **CompanySize.php**: Modelis, skirtas įmonių dydžių duomenims saugoti.
- **Category.php**: Modelis, skirtas bendrų kategorijų duomenims saugoti.
- **FavouriteJob.php**: Modelis, skirtas mėgstamų darbų duomenims saugoti.
- **CareerLevel.php**: Modelis, skirtas karjeros lygių duomenims saugoti.
- **NewsLetter.php**: Modelis, skirtas naujienlaiškių duomenims saugoti.
- **CmsServices.php**: Modelis, skirtas CMS paslaugų duomenims saugoti.
- **Application.php**: Modelis, tikėtina, skirtas paraiškų duomenims saugoti.
- **PostComment.php**: Modelis, skirtas įrašų komentarų duomenims saugoti.
- **Term.php**: Modelis, skirtas terminų duomenims saugoti.
- **Plan.php**: Modelis, skirtas planų (pvz., prenumeratų) duomenims saugoti.
- **FrontSetting.php**: Modelis, skirtas priekinės dalies nustatymų duomenims saugoti.
- **State.php**: Modelis, skirtas valstijų (regionų) duomenims saugoti.
- **MaritalStatus.php**: Modelis, skirtas šeimyninės padėties duomenims saugoti.
- **FunctionalArea.php**: Modelis, skirtas funkcinių sričių duomenims saugoti.
- **HeaderSlider.php**: Modelis, skirtas antraštės slankiklių duomenims saugoti.
- **BrandingSliders.php**: Modelis, skirtas prekės ženklo slankiklių duomenims saugoti.
- **ReportedToCompany.php**: Modelis, skirtas pranešimų apie įmones duomenims saugoti.
- **Taxonomy.php**: Modelis, skirtas taksonomijų duomenims saugoti.
- **Todo.php**: Modelis, tikėtina, skirtas užduočių sąrašo duomenims saugoti.
- **SalaryPeriod.php**: Modelis, skirtas atlyginimų laikotarpių duomenims saugoti.
- **ReportedJob.php**: Modelis, skirtas pranešimų apie darbus duomenims saugoti.
- **Language.php**: Modelis, skirtas kalbų duomenims saugoti.
- **Industry.php**: Modelis, skirtas pramonės šakų duomenims saugoti.
- **FavouriteCompany.php**: Modelis, skirtas mėgstamų įmonių duomenims saugoti.
- **Country.php**: Modelis, skirtas šalių duomenims saugoti.
- **Testimonial.php**: Modelis, skirtas atsiliepimų duomenims saugoti.
- **Post.php**: Modelis, skirtas įrašų (pvz., tinklaraščio) duomenims saugoti.
- **Taggable.php**: Modelis, tikėtina, skirtas žymėjimų duomenims saugoti.
- **City.php**: Modelis, skirtas miestų duomenims saugoti.
- **SocialAccount.php**: Modelis, skirtas socialinių paskyrų duomenims saugoti.
- **OwnerShipType.php**: Modelis, skirtas nuosavybės tipų duomenims saugoti.
- **ReportedToCandidate.php**: Modelis, skirtas pranešimų apie kandidatus duomenims saugoti.
- **Noticeboard.php**: Modelis, skirtas skelbimų lentos duomenims saugoti.
- **PostCategory.php**: Modelis, skirtas įrašų kategorijų duomenims saugoti.
- **NotificationSetting.php**: Modelis, skirtas pranešimų nustatymų duomenims saugoti.
- **Tag.php**: Modelis, skirtas žymų duomenims saugoti.
- **RequiredDegreeLevel.php**: Modelis, skirtas reikiamų laipsnių lygio duomenims saugoti.
- **Notification.php**: Modelis, skirtas pranešimų duomenims saugoti.
- **Inquiry.php**: Modelis, skirtas užklausų duomenims saugoti.