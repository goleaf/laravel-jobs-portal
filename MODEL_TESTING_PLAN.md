# Model Testing Plan - Laravel Job Portal

## 🧪 **COMPREHENSIVE MODEL TESTING STRATEGY**

### **Testing Overview**
This plan validates all 14 enhanced models with their 120+ custom scopes and modern Laravel 12 patterns.

---

## 📋 **CORE MODELS TESTING**

### **1. User Model Testing**
```php
// Test enhanced scopes
User::byLocation(1, 2)->get();           // Location filtering
User::search('john')->get();             // Name/email search
User::recent(7)->get();                  // Recent users (7 days)
User::withProfileImage()->get();         // Users with profile images
User::candidates()->get();               // Candidate users
User::employers()->get();                // Employer users
User::admins()->get();                   // Admin users
User::byGender('male')->get();           // Gender filtering
User::byLanguage('en')->get();           // Language filtering
User::popular(10)->get();                // Most popular users
```

### **2. Job Model Testing**
```php
// Test advanced job scopes
Job::byExperience('mid')->get();         // Experience level filtering
Job::remote()->get();                    // Remote jobs only
Job::withSalary()->get();                // Jobs with salary info
Job::urgent()->get();                    // Urgent jobs
Job::keywordSearch('developer')->get();  // Keyword search
Job::today()->get();                     // Today's jobs
Job::thisWeek()->get();                  // This week's jobs
Job::thisMonth()->get();                 // This month's jobs
Job::filter([
    'experience_level' => 'senior',
    'job_type' => 'full-time',
    'salary_min' => 50000,
    'location' => 'New York'
])->get();                               // Complex filtering
```

### **3. Company Model Testing**
```php
// Test company scopes
Company::active()->get();                // Active companies
Company::featured()->get();              // Featured companies
Company::byIndustry(1)->get();           // Industry filtering
Company::bySize('medium')->get();        // Company size filtering
Company::establishedBetween(2010, 2020)->get(); // Establishment range
Company::withWebsite()->get();           // Companies with websites
Company::byLocation(1, 2)->get();        // Location filtering
Company::withJobs()->get();              // Companies with jobs
Company::withActiveJobs()->get();        // Companies with active jobs
Company::search('tech')->get();          // Company search
Company::recent(30)->get();              // Recent companies
```

---

## 📋 **LOCATION MODELS TESTING**

### **4. State Model Testing**
```php
// Test state scopes
State::active()->get();                  // Active states
State::byCountry(1)->get();              // States by country
State::withUsers()->get();               // States with users
State::withCities()->get();              // States with cities
State::search('california')->get();      // State search
State::alphabetical()->get();            // Alphabetical order
State::popular(10)->get();               // Popular states
```

### **5. City Model Testing**
```php
// Test city scopes
City::active()->get();                   // Active cities
City::byState(1)->get();                 // Cities by state
City::byCountry(1)->get();               // Cities by country (through state)
City::withUsers()->get();                // Cities with users
City::search('new york')->get();         // City search
City::alphabetical()->get();             // Alphabetical order
City::popular(10)->get();                // Popular cities
City::major(100)->get();                 // Major cities (min users)
```

### **6. Country Model Testing**
```php
// Test country scopes
Country::active()->get();                // Active countries
Country::withUsers()->get();             // Countries with users
Country::withStates()->get();            // Countries with states
Country::search('united')->get();        // Country search
Country::byShortCode('US')->get();       // By country code
Country::withPhoneCode()->get();         // Countries with phone codes
Country::alphabetical()->get();          // Alphabetical order
Country::popular(10)->get();             // Popular countries
```

---

## 📋 **CATEGORY MODELS TESTING**

### **7. Industry Model Testing**
```php
// Test industry scopes
Industry::active()->get();               // Active industries
Industry::default()->get();              // Default industries
Industry::custom()->get();               // Custom industries
Industry::withCompanies()->get();        // Industries with companies
Industry::withCandidates()->get();       // Industries with candidates
Industry::search('technology')->get();   // Industry search
Industry::popular(10)->get();            // Popular industries
Industry::alphabetical()->get();         // Alphabetical order
```

### **8. FunctionalArea Model Testing**
```php
// Test functional area scopes
FunctionalArea::active()->get();         // Active functional areas
FunctionalArea::default()->get();        // Default functional areas
FunctionalArea::custom()->get();         // Custom functional areas
FunctionalArea::withJobs()->get();       // Areas with jobs
FunctionalArea::withCandidates()->get(); // Areas with candidates
FunctionalArea::search('marketing')->get(); // Area search
FunctionalArea::popular(10)->get();      // Popular areas
FunctionalArea::alphabetical()->get();   // Alphabetical order
```

### **9. JobCategory Model Testing**
```php
// Test job category scopes
JobCategory::active()->get();            // Active categories
JobCategory::featured()->get();          // Featured categories
JobCategory::notFeatured()->get();       // Non-featured categories
JobCategory::default()->get();           // Default categories
JobCategory::custom()->get();            // Custom categories
JobCategory::withJobs()->get();          // Categories with jobs
JobCategory::withActiveJobs()->get();    // Categories with active jobs
JobCategory::search('software')->get();  // Category search
JobCategory::popular(10)->get();         // Popular categories
JobCategory::alphabetical()->get();      // Alphabetical order
JobCategory::recent(30)->get();          // Recent categories
```

---

## 📋 **APPLICATION MODELS TESTING**

### **10. Candidate Model Testing**
```php
// Test candidate scopes
Candidate::active()->get();              // Active candidates
Candidate::available()->get();           // Available candidates
Candidate::availableByDate('2024-01-01')->get(); // Available from date
Candidate::byExperience('senior')->get(); // Experience level
Candidate::byCareerLevel('manager')->get(); // Career level
Candidate::bySalaryRange(50000, 100000)->get(); // Salary range
Candidate::byLocation(1, 2)->get();      // Location filtering
Candidate::withResume()->get();          // Candidates with resume
Candidate::search('developer')->get();   // Candidate search
Candidate::withJobAlerts()->get();       // Candidates with job alerts
```

### **11. JobApplication Model Testing**
```php
// Test job application scopes
JobApplication::byStatus('pending')->get(); // By status
JobApplication::pending()->get();        // Pending applications
JobApplication::hired()->get();          // Hired applications
JobApplication::rejected()->get();       // Rejected applications
JobApplication::shortlisted()->get();    // Shortlisted applications
JobApplication::recent(7)->get();        // Recent applications
JobApplication::byJob(1)->get();         // Applications for specific job
JobApplication::byCandidate(1)->get();   // Applications by candidate
JobApplication::bySalaryRange(50000, 100000)->get(); // Salary range
JobApplication::withNotes()->get();      // Applications with notes
JobApplication::byCompany(1)->get();     // Applications by company
JobApplication::today()->get();          // Today's applications
JobApplication::thisWeek()->get();       // This week's applications
JobApplication::thisMonth()->get();      // This month's applications
```

---

## 📋 **SYSTEM MODELS TESTING**

### **12. Setting Model Testing**
```php
// Test setting scopes
Setting::byKey('site_name')->get();      // By specific key
Setting::byKeyPattern('email_%')->get(); // By key pattern
Setting::global()->get();                // Global settings
Setting::userSpecific(1)->get();         // User-specific settings
Setting::theme()->get();                 // Theme settings
Setting::email()->get();                 // Email settings
Setting::socialMedia()->get();           // Social media settings
Setting::payment()->get();               // Payment settings
```

### **13. Plan Model Testing**
```php
// Test plan scopes
Plan::active()->get();                   // Active plans
Plan::trial()->get();                    // Trial plans
Plan::paid()->get();                     // Paid plans
Plan::free()->get();                     // Free plans
Plan::featured()->get();                 // Featured plans
Plan::byPriceRange(0, 100)->get();       // Price range
Plan::byJobAllowance(10, 50)->get();     // Job allowance range
Plan::popular(5)->get();                 // Popular plans
Plan::orderByPrice('asc')->get();        // Ordered by price
Plan::withActiveSubscriptions()->get();  // Plans with active subscriptions
```

### **14. Skill Model Testing**
```php
// Test skill scopes
Skill::active()->get();                  // Active skills
Skill::default()->get();                 // Default skills
Skill::custom()->get();                  // Custom skills
Skill::popular(20)->get();               // Popular skills
Skill::search('javascript')->get();      // Skill search
Skill::usedInJobs()->get();              // Skills used in jobs
Skill::usedByCandidates()->get();        // Skills used by candidates
Skill::alphabetical()->get();            // Alphabetical order
```

---

## 🧪 **AUTOMATED TESTING SCRIPT**

### **Laravel Artisan Command Testing**
```bash
# Create test command to validate all scopes
php artisan make:command TestModelScopes

# Run comprehensive model testing
php artisan test:model-scopes

# Performance testing for scope queries
php artisan test:scope-performance
```

### **PHPUnit Model Tests**
```php
// Create comprehensive test cases
tests/Unit/Models/UserModelTest.php
tests/Unit/Models/JobModelTest.php
tests/Unit/Models/CompanyModelTest.php
// ... etc for all 14 models
```

---

## 📊 **TESTING METRICS & VALIDATION**

### **Performance Benchmarks**
- **Query Execution Time**: < 100ms for simple scopes
- **Complex Query Time**: < 500ms for multi-scope chains
- **Memory Usage**: < 50MB for scope operations
- **Database Hits**: Minimize N+1 queries

### **Functionality Validation**
- ✅ All 120+ scopes execute without errors
- ✅ Proper query builder chains work
- ✅ Type casting works correctly
- ✅ Relationships load properly
- ✅ Search functionality returns relevant results

### **Code Quality Checks**
- ✅ All models use modern casts() methods
- ✅ Consistent scope naming conventions
- ✅ Proper return type hints
- ✅ DocBlock documentation complete
- ✅ Laravel 12 best practices followed

---

## 🚀 **NEXT STEPS**

1. **Create PHPUnit tests** for all model enhancements
2. **Performance optimization** for complex scope chains
3. **Documentation updates** with usage examples
4. **Integration testing** with controllers and API endpoints
5. **Real-world data testing** with production-like datasets

**Testing Priority**: Focus on User, Job, Company, and JobApplication models first as they are the most critical for the job portal functionality. 