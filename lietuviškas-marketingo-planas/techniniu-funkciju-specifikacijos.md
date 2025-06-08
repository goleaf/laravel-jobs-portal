# Techninių Funkcijų Specifikacijos

## 🏗️ Sistemos Architektūra

### Duomenų Bazės Schema

#### Pagrindinės Lentelės

##### Users (Vartotojai)
```sql
- id (PK)
- first_name (VARCHAR 255)
- last_name (VARCHAR 255) 
- email (VARCHAR 255, UNIQUE)
- phone (VARCHAR 20)
- password (HASHED)
- user_type (ENUM: admin, employer, candidate)
- country_id (FK)
- state_id (FK) 
- city_id (FK)
- is_active (BOOLEAN)
- is_verified (BOOLEAN)
- email_verified_at (TIMESTAMP)
- language (VARCHAR 5, DEFAULT 'en')
- profile_views (INT, DEFAULT 0)
- social_urls (JSON)
- created_at, updated_at, deleted_at
```

##### Jobs (Darbo Skelbimai)
```sql
- id (PK)
- job_id (VARCHAR 20, UNIQUE) - automatinis generavimas
- company_id (FK)
- job_title (VARCHAR 180)
- description (TEXT)
- key_responsibilities (TEXT)
- job_category_id (FK)
- job_type_id (FK) - nuolatinis, laikinas, freelance
- salary_from (DECIMAL 12,2)
- salary_to (DECIMAL 12,2)
- currency_id (FK)
- salary_period_id (FK) - valandinis, mėnesinis, metinis
- country_id (FK)
- state_id (FK)
- city_id (FK)
- experience (INT) - metais
- position (INT) - pozicijų kiekis
- degree_level_id (FK)
- career_level_id (FK)
- functional_area_id (FK)
- job_shift_id (FK)
- job_expiry_date (DATE)
- status (ENUM: draft, live, closed, paused)
- is_featured (BOOLEAN)
- is_freelance (BOOLEAN)
- is_suspended (BOOLEAN)
- hide_salary (BOOLEAN)
- no_preference (TINYINT) - lytis: 0-female, 1-male, 2-both
- views_count (INT, DEFAULT 0)
- applications_count (INT, DEFAULT 0)
- created_at, updated_at, deleted_at
```

##### Companies (Įmonės)
```sql
- id (PK)
- user_id (FK)
- name (VARCHAR 180) - iš users lentelės
- ceo (VARCHAR 180)
- industry_id (FK)
- company_size_id (FK)
- ownership_type_id (FK)
- established_in (YEAR)
- no_of_offices (INT)
- website (URL)
- details (TEXT)
- location (VARCHAR 255)
- location2 (VARCHAR 255)
- fax (VARCHAR 20)
- logo_path (VARCHAR 255)
- social_media (JSON) - facebook, twitter, linkedin, etc.
- is_featured (BOOLEAN)
- created_at, updated_at
```

##### Candidates (Kandidatai)
```sql
- id (PK)
- user_id (FK)
- career_level_id (FK)
- industry_id (FK)
- functional_area_id (FK)
- current_salary (DECIMAL 12,2)
- expected_salary (DECIMAL 12,2)
- salary_currency_id (FK)
- immediate_available (BOOLEAN)
- experience (INT) - metais
- marital_status_id (FK)
- national_id_card (VARCHAR 50)
- father_name (VARCHAR 100)
- date_of_birth (DATE)
- gender (TINYINT) - 0-male, 1-female
- image_path (VARCHAR 255)
- video_path (VARCHAR 255)
- available_at (DATE)
- created_at, updated_at
```

#### Papildomos Lentelės

##### Job_Applications (Darbo Prašymai)
```sql
- id (PK)
- job_id (FK)
- candidate_id (FK) 
- resume_id (FK)
- expected_salary (DECIMAL 12,2)
- notes (TEXT)
- status (ENUM: pending, reviewed, interview, hired, rejected)
- applied_at (TIMESTAMP)
- reviewed_at (TIMESTAMP)
- notes_from_employer (TEXT)
- created_at, updated_at
```

##### Skills & Relations (Įgūdžiai)
```sql
Skills:
- id (PK), name (VARCHAR 100), description (TEXT)
- is_active (BOOLEAN), is_default (BOOLEAN)

Jobs_Skills (Many-to-Many):
- job_id (FK), skill_id (FK)

Candidate_Skills (Many-to-Many):
- user_id (FK), skill_id (FK)
- proficiency_level (ENUM: beginner, intermediate, advanced, expert)
```

### API Endpoints Specifikacijos

#### Autentifikacijos Endpoints
```
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
POST /api/auth/verify-email
POST /api/auth/forgot-password
POST /api/auth/reset-password
GET  /api/auth/me
```

#### Darbo Skelbimų API
```
GET    /api/jobs              - visi darbo skelbimai su filtrais
POST   /api/jobs              - sukurti naują (employer)
GET    /api/jobs/{id}         - konkretus darbo skelbimas
PUT    /api/jobs/{id}         - atnaujinti (employer)
DELETE /api/jobs/{id}         - ištrinti (employer)
POST   /api/jobs/{id}/apply   - pateikti prašymą (candidate)
GET    /api/jobs/featured     - išskirtiniai darbo skelbimai
GET    /api/jobs/my           - mano darbo skelbimai (employer)
GET    /api/jobs/applications - mano prašymai (candidate)
```

#### Įmonių API
```
GET    /api/companies         - visos įmonės su filtrais
POST   /api/companies         - sukurti profil (employer)
GET    /api/companies/{id}    - konkreti įmonė
PUT    /api/companies/{id}    - atnaujinti (employer)
POST   /api/companies/{id}/follow - sekti įmonę (candidate)
GET    /api/companies/my      - mano įmonė (employer)
```

#### Kandidatų API
```
GET    /api/candidates        - visi kandidatai (employer)
POST   /api/candidates        - sukurti profil (candidate)
GET    /api/candidates/{id}   - konkretus kandidatas
PUT    /api/candidates/{id}   - atnaujinti (candidate)
GET    /api/candidates/my     - mano profilis (candidate)
```

#### Paieškos API
```
GET    /api/search/jobs       - darbo paieška
GET    /api/search/companies  - įmonių paieška  
GET    /api/search/candidates - kandidatų paieška (employer)
GET    /api/search/suggestions - paieškos pasiūlymai
```

### Frontend Komponentų Architektūra

#### Vue.js 3 Kompozicijos

##### Layouts
```typescript
// AppLayout.vue - pagrindinis layout
// AuthLayout.vue - autentifikacijos puslapiai
// AdminLayout.vue - administratoriaus sąsaja
// CandidateLayout.vue - kandidato dashboard
// EmployerLayout.vue - darbdavio dashboard
```

##### Core Components
```typescript
// Navigation
- AppHeader.vue
- AppSidebar.vue  
- AppFooter.vue
- BreadcrumbNav.vue
- LanguageSwitcher.vue

// Forms
- BaseInput.vue
- BaseSelect.vue
- BaseTextarea.vue
- BaseCheckbox.vue
- BaseRadio.vue
- FileUpload.vue
- RichTextEditor.vue

// UI Elements
- BaseButton.vue
- BaseModal.vue
- BaseCard.vue
- BaseBadge.vue
- BaseAlert.vue
- Pagination.vue
- DataTable.vue
- SkeletonLoader.vue

// Job Related
- JobCard.vue
- JobList.vue
- JobFilters.vue
- JobDetails.vue
- JobApplication.vue
- ApplicationStatus.vue

// Company Related  
- CompanyCard.vue
- CompanyList.vue
- CompanyProfile.vue
- CompanyJobs.vue

// Candidate Related
- CandidateCard.vue
- CandidateProfile.vue
- CandidateResume.vue
- SkillSelector.vue
- ExperienceForm.vue
- EducationForm.vue
```

#### Pinia Stores

##### Auth Store
```typescript
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    isAuthenticated: false,
    permissions: []
  }),
  
  actions: {
    async login(credentials),
    async register(userData),
    async logout(),
    async fetchUser(),
    async updateProfile(data),
    checkPermission(permission)
  }
})
```

##### Jobs Store
```typescript
export const useJobsStore = defineStore('jobs', {
  state: () => ({
    jobs: [],
    currentJob: null,
    filters: {},
    pagination: {},
    loading: false
  }),
  
  actions: {
    async fetchJobs(filters),
    async createJob(jobData),
    async updateJob(id, data),
    async deleteJob(id),
    async applyToJob(jobId, applicationData),
    setFilters(filters)
  }
})
```

##### Companies Store
```typescript
export const useCompaniesStore = defineStore('companies', {
  state: () => ({
    companies: [],
    currentCompany: null,
    myCompany: null,
    loading: false
  }),
  
  actions: {
    async fetchCompanies(filters),
    async createCompany(data),
    async updateCompany(data),
    async followCompany(id),
    async unfollowCompany(id)
  }
})
```

## 🔐 Saugumo Specifikacijos

### Autentifikacijos Sistema

#### Laravel Sanctum Konfigūracija
```php
// config/sanctum.php
'expiration' => 60 * 24, // 24 valandos
'middleware' => [
    'verify_csrf_token' => false,
    'encrypt_cookies' => false,
    'throttle_requests' => true,
],
'guard' => ['web'],
```

#### Rate Limiting
```php
// RouteServiceProvider.php
RateLimiter::for('api', function (Request $request) {
    return $request->user()
        ? Limit::perMinute(120)->by($request->user()->id)
        : Limit::perMinute(60)->by($request->ip());
});

RateLimiter::for('login', function (Request $request) {
    return [
        Limit::perMinute(10)->by($request->ip()),
        Limit::perMinute(3)->by($request->string('email')),
    ];
});
```

#### Duomenų Validacijos Taisyklės

##### Job Creation Validation
```php
public function rules(): array
{
    return [
        'job_title' => 'required|string|max:180',
        'description' => 'required|string|min:100',
        'company_id' => 'required|exists:companies,id',
        'job_category_id' => 'required|exists:job_categories,id',
        'job_type_id' => 'required|exists:job_types,id',
        'salary_from' => 'required|numeric|min:0|max:999999999',
        'salary_to' => 'required|numeric|min:0|max:999999999|gte:salary_from',
        'currency_id' => 'required|exists:salary_currencies,id',
        'country_id' => 'required|exists:countries,id',
        'state_id' => 'required|exists:states,id',
        'city_id' => 'required|exists:cities,id',
        'experience' => 'required|integer|min:0|max:50',
        'position' => 'required|integer|min:1|max:100',
        'job_expiry_date' => 'required|date|after:today',
        'skills' => 'array|min:1|max:10',
        'skills.*' => 'exists:skills,id'
    ];
}
```

##### User Registration Validation
```php
public function rules(): array
{
    return [
        'first_name' => 'required|string|max:100',
        'last_name' => 'nullable|string|max:100',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\-\(\)\s]+$/',
        'password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
            Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()
        ],
        'user_type' => 'required|in:employer,candidate',
        'country_id' => 'required|exists:countries,id',
        'terms_accepted' => 'required|accepted'
    ];
}
```

### XSS ir CSRF Apsauga

#### Content Security Policy
```php
// config/app.php
'csp' => [
    'default-src' => "'self'",
    'script-src' => "'self' 'unsafe-inline' https://js.stripe.com",
    'style-src' => "'self' 'unsafe-inline' https://fonts.googleapis.com",
    'img-src' => "'self' data: https:",
    'font-src' => "'self' https://fonts.gstatic.com",
    'connect-src' => "'self' https://api.stripe.com",
    'frame-src' => "'self' https://js.stripe.com"
]
```

#### Input Sanitization
```php
// BaseRequest.php
protected function prepareForValidation()
{
    $this->merge([
        'job_title' => strip_tags($this->job_title),
        'description' => clean($this->description), // HTMLPurifier
        'expected_salary' => str_replace(',', '', $this->expected_salary)
    ]);
}
```

## 📊 Duomenų Bazės Optimizacijos

### Indeksai ir Optimizacija

#### Kritiniai Indeksai
```sql
-- Jobs lentelė
CREATE INDEX idx_jobs_status_expiry ON jobs(status, job_expiry_date);
CREATE INDEX idx_jobs_location ON jobs(country_id, state_id, city_id);
CREATE INDEX idx_jobs_salary ON jobs(salary_from, salary_to, currency_id);
CREATE INDEX idx_jobs_category_type ON jobs(job_category_id, job_type_id);
CREATE INDEX idx_jobs_featured ON jobs(is_featured, status, job_expiry_date);

-- Users lentelė  
CREATE INDEX idx_users_type_active ON users(user_type, is_active);
CREATE INDEX idx_users_location ON users(country_id, state_id, city_id);
CREATE INDEX idx_users_email_verified ON users(email_verified_at);

-- Job Applications lentelė
CREATE INDEX idx_applications_job_status ON job_applications(job_id, status);
CREATE INDEX idx_applications_candidate ON job_applications(candidate_id, applied_at);
```

#### Query Optimizacijos

##### Darbo Skelbimų Paieška
```php
// JobRepository.php
public function searchJobs(array $filters): Collection
{
    return Job::query()
        ->select([
            'jobs.*',
            'companies.name as company_name',
            'companies.logo_path',
            'job_categories.name as category_name',
            'job_types.name as type_name'
        ])
        ->join('companies', 'jobs.company_id', '=', 'companies.id')
        ->join('job_categories', 'jobs.job_category_id', '=', 'job_categories.id')
        ->join('job_types', 'jobs.job_type_id', '=', 'job_types.id')
        ->when($filters['keyword'] ?? null, function ($query, $keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('jobs.job_title', 'LIKE', "%{$keyword}%")
                  ->orWhere('jobs.description', 'LIKE', "%{$keyword}%")
                  ->orWhere('companies.name', 'LIKE', "%{$keyword}%");
            });
        })
        ->when($filters['location'] ?? null, function ($query, $location) {
            $query->where('jobs.city_id', $location);
        })
        ->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('jobs.job_category_id', $category);
        })
        ->when($filters['salary_min'] ?? null, function ($query, $salary) {
            $query->where('jobs.salary_from', '>=', $salary);
        })
        ->when($filters['salary_max'] ?? null, function ($query, $salary) {
            $query->where('jobs.salary_to', '<=', $salary);
        })
        ->where('jobs.status', Job::STATUS_OPEN)
        ->where('jobs.is_suspended', false)
        ->where('jobs.job_expiry_date', '>', now())
        ->orderByDesc('jobs.is_featured')
        ->orderByDesc('jobs.created_at')
        ->paginate(20);
}
```

### Cache Strategijos

#### Redis Cache Implementacija
```php
// JobService.php
public function getFeaturedJobs(): Collection
{
    return Cache::tags(['jobs', 'featured'])
        ->remember('jobs.featured', 3600, function () {
            return Job::with(['company', 'jobCategory', 'jobType'])
                ->featured()
                ->limit(10)
                ->get();
        });
}

public function getJobById(int $id): ?Job
{
    return Cache::tags(['jobs', "job-{$id}"])
        ->remember("job.{$id}", 1800, function () use ($id) {
            return Job::with([
                'company',
                'jobCategory', 
                'jobType',
                'jobsSkill',
                'country',
                'state', 
                'city'
            ])->find($id);
        });
}

// Cache invalidation
public function updateJob(int $id, array $data): Job
{
    $job = Job::findOrFail($id);
    $job->update($data);
    
    // Clear related caches
    Cache::tags(['jobs', "job-{$id}"])->flush();
    Cache::forget('jobs.featured');
    Cache::forget('jobs.latest');
    
    return $job->fresh();
}
```

## 🌐 Daugiakalbės Sistemos Specifikacijos

### Vertimų Architektūra

#### JSON Vertimų Struktūra
```json
{
  "navigation": {
    "home": "Pagrindinis",
    "jobs": "Darbo Vietos", 
    "companies": "Įmonės",
    "candidates": "Kandidatai",
    "about": "Apie Mus",
    "contact": "Kontaktai"
  },
  "auth": {
    "login": "Prisijungti",
    "register": "Registruotis",
    "logout": "Atsijungti",
    "forgot_password": "Pamiršau Slaptažodį",
    "reset_password": "Atkurti Slaptažodį"
  },
  "jobs": {
    "title": "Darbo Pavadinimas",
    "description": "Aprašymas", 
    "company": "Įmonė",
    "location": "Vieta",
    "salary": "Atlyginimas",
    "apply": "Teikti Prašymą",
    "deadline": "Terminas",
    "requirements": "Reikalavimai",
    "benefits": "Privalumai"
  }
}
```

#### Laravel Lokalizacijos Konfigūracija
```php
// config/app.php
'locale' => 'lt',
'fallback_locale' => 'en',
'available_locales' => [
    'en' => 'English',
    'lt' => 'Lietuvių',
    'ru' => 'Русский',
    'de' => 'Deutsch',
    'es' => 'Español',
    'fr' => 'Français',
    'pt' => 'Português',
    'ar' => 'العربية',
    'zh' => '中文',
    'tr' => 'Türkçe'
],

// Middleware
'middleware' => [
    \App\Http\Middleware\SetLocale::class,
]
```

#### Vue i18n Konfigūracija
```typescript
// i18n.ts
import { createI18n } from 'vue-i18n'

const messages = {
  en: () => import('./locales/en.json'),
  lt: () => import('./locales/lt.json'),
  ru: () => import('./locales/ru.json'),
  // ... kitos kalbos
}

export const i18n = createI18n({
  legacy: false,
  locale: localStorage.getItem('locale') || 'lt',
  fallbackLocale: 'en',
  messages
})

// Komponentuose naudojimas:
// const { t, locale } = useI18n()
```

### RTL Palaikymas Arabų Kalbai

#### CSS RTL Konfigūracija
```scss
// tailwind.config.js
module.exports = {
  content: ['./resources/**/*.{vue,js,ts,jsx,tsx,blade.php}'],
  theme: {
    extend: {
      direction: {
        'rtl': 'rtl',
        'ltr': 'ltr'
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    function({ addUtilities }) {
      addUtilities({
        '.rtl': { direction: 'rtl' },
        '.ltr': { direction: 'ltr' }
      })
    }
  ]
}

// RTL stiliaus komponentai
.rtl {
  .text-right { text-align: right; }
  .text-left { text-align: left; }
  .float-right { float: left; }
  .float-left { float: right; }
  .ml-4 { margin-right: 1rem; margin-left: 0; }
  .mr-4 { margin-left: 1rem; margin-right: 0; }
}
```

## 📱 Mobiliojo Dizaino Specifikacijos

### Responsive Breakpoints
```scss
// TailwindCSS breakpoints
sm: '640px',   // Mobilūs telefonai (landscape)
md: '768px',   // Planšetės
lg: '1024px',  // Mažesni desktop
xl: '1280px',  // Desktop
2xl: '1536px'  // Dideli ekranai
```

### PWA Konfigūracija
```typescript
// vite.config.ts
import { VitePWA } from 'vite-plugin-pwa'

export default defineConfig({
  plugins: [
    VitePWA({
      registerType: 'autoUpdate',
      workbox: {
        globPatterns: ['**/*.{js,css,html,ico,png,svg}']
      },
      manifest: {
        name: 'Darbo Portalas',
        short_name: 'DarboPortalas',
        description: 'Profesionalus darbo paieškos portalas',
        theme_color: '#1f2937',
        background_color: '#ffffff',
        display: 'standalone',
        icons: [
          {
            src: '/icons/icon-192x192.png',
            sizes: '192x192',
            type: 'image/png'
          },
          {
            src: '/icons/icon-512x512.png', 
            sizes: '512x512',
            type: 'image/png'
          }
        ]
      }
    })
  ]
})
```

---

*Šis techninis dokumentas apibūdina detalią sistemos architektūrą ir implementacijos specifikacijas.* 