# SQLite Database Setup and Seeding Guide

## 🚀 Quick Setup

Run the automated setup script that handles everything:

```bash
php setup_sqlite_and_seed.php
```

This single command will:
- ✅ Create `.env` file with SQLite configuration
- ✅ Create SQLite database file
- ✅ Generate application key
- ✅ Run all migrations
- ✅ Create storage links
- ✅ Seed database with optimized data
- ✅ Clear all caches

## 📊 What Gets Seeded (SQLite Optimized)

The SQLite-optimized seeder creates approximately **1,000-1,500 records** across all tables:

### Core Data
- **20 Countries** with states and cities
- **153 Users** (3 admins, 50 employers, 100 candidates)
- **40 Companies** with full profiles
- **System Settings** (33 settings total)

### Master Data (12 Tables)
- Industries (12), Company Sizes (6), Job Types (6)
- Career Levels (6), Languages (10), Skills (49)
- Salary Currencies (10), Job Shifts (4)
- And more reference data

### Job Portal Core
- **15 Job Categories** (Software, Marketing, Finance, etc.)
- **200 Jobs** with skills attached
- **80 Candidates** with education and experience
- **300 Job Applications**
- **3 Subscription Plans**

## 🎯 SQLite Optimizations

- **Reduced Record Counts**: Optimized for SQLite performance
- **Proper Foreign Keys**: SQLite PRAGMA handling
- **Efficient Relationships**: Minimal but realistic data connections
- **Fast Seeding**: Typically completes in under 2 minutes

## 📝 Manual Setup (Alternative)

If you prefer manual setup:

### 1. Create .env file
```bash
# Copy and create .env with SQLite config
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
DB_FOREIGN_KEYS=true
```

### 2. Create Database
```bash
touch database/database.sqlite
```

### 3. Run Setup
```bash
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan db:seed --class=SQLiteOptimizedSeeder
```

## 🔍 Database Information

After setup, your SQLite database will be located at:
- **File**: `database/database.sqlite`
- **Size**: Typically 2-4 MB
- **Tables**: 60+ tables with relationships
- **Records**: ~1,000-1,500 total records

## 🚀 Usage

### Start the Application
```bash
php artisan serve
```

### Access the Application
- **URL**: http://localhost:8000
- **Admin Users**: Check database for admin accounts (user_type = 1)
- **Employers**: 50 employer accounts (user_type = 2)
- **Candidates**: 100 candidate accounts (user_type = 3)

### Sample Data Available
- ✅ Job browsing with 200 realistic job postings
- ✅ Company profiles with industry classifications
- ✅ Candidate profiles with skills and experience
- ✅ Job application workflows
- ✅ Complete master data for all dropdowns

## 🛠️ Customization

### Adjust Record Counts
Edit `database/seeders/SQLiteOptimizedSeeder.php`:

```php
// Change job count from 200 to 500
for ($i = 0; $i < 500; $i++) {
    // Job creation logic
}
```

### Use Full Comprehensive Seeder
If you want all features (slower but complete):

```bash
php artisan db:seed --class=ComprehensiveAllTablesSeeder
```

## 📈 Performance

**SQLite Optimized Seeder**:
- ⚡ Fast: ~1-2 minutes
- 💾 Light: ~2-4 MB database
- 🎯 Focused: Core functionality

**Comprehensive Seeder**:
- 🐌 Slower: ~3-5 minutes  
- 💾 Larger: ~8-15 MB database
- 🌟 Complete: All features

## 🔧 Troubleshooting

### Database Locked Error
```bash
# Stop any running Laravel processes
# Delete and recreate the database file
rm database/database.sqlite
touch database/database.sqlite
php artisan migrate
```

### Permission Issues
```bash
# Ensure database directory is writable
chmod 755 database/
chmod 664 database/database.sqlite
```

### Missing Dependencies
```bash
# Install required packages
composer install
npm install
```

## ✅ Verification

Check if everything is working:

```bash
# Check database tables
php artisan tinker
>>> DB::select('SELECT name FROM sqlite_master WHERE type="table"')

# Check record counts
>>> App\Models\User::count()
>>> App\Models\Job::count()
>>> App\Models\Company::count()
```

## 🎉 Success!

Your Laravel Job Portal is now running with SQLite and seeded with realistic data. You can:

- Browse jobs and companies
- Test user registration and login
- Explore job application workflows
- Test admin, employer, and candidate features
- Use all master data dropdowns

Perfect for development, testing, and demonstration purposes!