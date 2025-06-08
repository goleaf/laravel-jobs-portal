# Context7 Test Database Setup

## Required for Test Execution

### 1. Create Test Database
```sql
CREATE DATABASE IF NOT EXISTS jobportal_test;
```

### 2. Update .env.testing
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jobportal_test
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 3. Run Migrations for Tests
```bash
php artisan migrate --env=testing
```

### 4. Run Specific Tests
```bash
# Run all FormRequest tests
vendor/bin/phpunit tests/Unit/Requests/

# Run specific controller group tests
vendor/bin/phpunit tests/Unit/Requests/Location/
vendor/bin/phpunit tests/Unit/Requests/MasterData/
vendor/bin/phpunit tests/Unit/Requests/Job/
vendor/bin/phpunit tests/Unit/Requests/Financial/

# Run Feature tests
vendor/bin/phpunit tests/Feature/Location/
vendor/bin/phpunit tests/Feature/MasterData/
vendor/bin/phpunit tests/Feature/Job/
vendor/bin/phpunit tests/Feature/Financial/
```

### 5. Test Coverage
```bash
vendor/bin/phpunit --coverage-html coverage-report/
```
