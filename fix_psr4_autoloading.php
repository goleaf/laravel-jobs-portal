<?php
/**
 * Fix PSR-4 Autoloading Issues
 * 
 * This script fixes namespace mismatches that cause PSR-4 autoloading violations
 */

$fixes = [
    // Content directory files
    'app/Http/Requests/Content/StorePostRequest.php' => 'App\\Http\\Requests\\Content',
    
    // Job directory files  
    'app/Http/Requests/Job/StoreJobTypeRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateJobTypeRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/DeleteJobTypeRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/StoreJobShiftRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateJobShiftRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/DeleteJobShiftRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/ApplyJobRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/CreateJobRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/CreateJobShiftRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateJobCategoryRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateJobJobRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateSlotJobApplicationRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/DeleteJobRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/StoreJobRequest.php' => 'App\\Http\\Requests\\Job',
    'app/Http/Requests/Job/UpdateJobRequest.php' => 'App\\Http\\Requests\\Job',
    
    // User directory files
    'app/Http/Requests/User/ChangePasswordUserRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/GetWebSocketAuthRealTimeRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/LoginAuthRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/RegisterAuthRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/ResetPasswordAuthRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/UpdatePasswordAuthRequest.php' => 'App\\Http\\Requests\\User',
    'app/Http/Requests/User/ChangePasswordRequest.php' => 'App\\Http\\Requests\\User',
    
    // Admin directory files
    'app/Http/Requests/Admin/StoreAdminRequest.php' => 'App\\Http\\Requests\\Admin',
    'app/Http/Requests/Admin/UpdateAdminRequest.php' => 'App\\Http\\Requests\\Admin',
    'app/Http/Requests/Admin/AdminCreateUserRequest.php' => 'App\\Http\\Requests\\Admin',
    'app/Http/Requests/Admin/AdminStoreUserRequest.php' => 'App\\Http\\Requests\\Admin',
    'app/Http/Requests/Admin/AdminUpdateUserRequest.php' => 'App\\Http\\Requests\\Admin',
    
    // Location directory files
    'app/Http/Requests/Location/StoreCountryRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/UpdateCountryRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/DeleteCountryRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/StoreStateRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/UpdateStateRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/DeleteStateRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/StoreCityRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/UpdateCityRequest.php' => 'App\\Http\\Requests\\Location',
    'app/Http/Requests/Location/DeleteCityRequest.php' => 'App\\Http\\Requests\\Location',
    
    // MasterData directory files
    'app/Http/Requests/MasterData/StoreIndustryRequest.php' => 'App\\Http\\Requests\\MasterData',
    'app/Http/Requests/MasterData/UpdateIndustryRequest.php' => 'App\\Http\\Requests\\MasterData',
    'app/Http/Requests/MasterData/DeleteIndustryRequest.php' => 'App\\Http\\Requests\\MasterData',
    'app/Http/Requests/MasterData/StoreFunctionalAreaRequest.php' => 'App\\Http\\Requests\\MasterData',
    'app/Http/Requests/MasterData/UpdateFunctionalAreaRequest.php' => 'App\\Http\\Requests\\MasterData',
    'app/Http/Requests/MasterData/DeleteFunctionalAreaRequest.php' => 'App\\Http\\Requests\\MasterData',
    
    // Candidate directory files
    'app/Http/Requests/Candidate/DeleteCandidateRequest.php' => 'App\\Http\\Requests\\Candidate',
    'app/Http/Requests/Candidate/StoreCandidateRequest.php' => 'App\\Http\\Requests\\Candidate',
    'app/Http/Requests/Candidate/UpdateCandidateRequest.php' => 'App\\Http\\Requests\\Candidate',
    
    // Financial directory files
    'app/Http/Requests/Financial/CreatePlanRequest.php' => 'App\\Http\\Requests\\Financial',
    'app/Http/Requests/Financial/StorePlanRequest.php' => 'App\\Http\\Requests\\Financial',
    'app/Http/Requests/Financial/UpdatePlanRequest.php' => 'App\\Http\\Requests\\Financial',
    'app/Http/Requests/Financial/DeletePlanRequest.php' => 'App\\Http\\Requests\\Financial',
    
    // Auth directory files  
    'app/Http/Requests/Auth/ForgotPasswordRequest.php' => 'App\\Http\\Requests\\Auth',
    'app/Http/Requests/Auth/ResetPasswordRequest.php' => 'App\\Http\\Requests\\Auth',
    
    // Contact directory files
    'app/Http/Requests/Contact/ContactFormRequest.php' => 'App\\Http\\Requests\\Contact',
    
    // Transaction directory files
    'app/Http/Requests/Transaction/UpdateTransactionRequest.php' => 'App\\Http\\Requests\\Transaction',
];

$repositoryFixes = [
    'app/Repositories/UserRepository.php' => 'App\\Repositories\\EnhancedUserRepository',
    'app/Repositories/BaseRepositoryInterface.php' => 'App\\Repositories\\EnhancedBaseRepository',
];

$controllerFixes = [
    'app/Http/Controllers/Location/CountryController.php' => 'App\\Http\\Controllers\\Location',
    'app/Http/Controllers/Location/StateController.php' => 'App\\Http\\Controllers\\Location',
    'app/Http/Controllers/User/CandidateController.php' => 'App\\Http\\Controllers\\User',
    'app/Http/Controllers/User/EmployerController.php' => 'App\\Http\\Controllers\\User',
    'app/Http/Controllers/Settings/SettingController.php' => 'App\\Http\\Controllers\\Settings',
    'app/Http/Controllers/Settings/LanguageController.php' => 'App\\Http\\Controllers\\Settings',
    'app/Http/Controllers/Settings/LocaleController.php' => 'App\\Http\\Controllers\\Settings',
    'app/Http/Controllers/Content/PostController.php' => 'App\\Http\\Controllers\\Content',
    'app/Http/Controllers/Content/PostCategoryController.php' => 'App\\Http\\Controllers\\Content',
    'app/Http/Controllers/MasterData/CareerLevelController.php' => 'App\\Http\\Controllers\\MasterData',
    'app/Http/Controllers/MasterData/CompanySizeController.php' => 'App\\Http\\Controllers\\MasterData',
];

$resourceFixes = [
    'app/Http/Resources/Job/JobResource.php' => 'App\\Http\\Resources\\Job',
];

$middlewareFixes = [
    'app/Http/Middleware/Context7SanctumConfig.php' => 'App\\Http\\Middleware\\UniversalSanctumConfig',
];

$modelFixes = [
    'app/ReportedToCandidate.php' => 'App\\Models\\ReportedToCandidate',
];

$commandFixes = [
    'app/Console/Commands_backup/CheckTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/CleanupRappasoftReferences.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/CleanupTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/ConsolidateTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/ConvertRappasoftTables.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/ConvertSvgToComponents.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/CreateLithuanianTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/ExtractSvgComponents.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/MigrateFromSpatie.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/MigrateJsonTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/MigrateMediaLibrary.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/OptimizeAssets.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/PerformanceReport.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/RateLimitStats.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/RedisMonitor.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/StandardizeJavaScript.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/StandardizeTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/SynchronizeTranslations.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/SystemOptimization.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/TranslationCommand.php' => 'App\\Console\\Commands',
    'app/Console/Commands_backup/WarmupCache.php' => 'App\\Console\\Commands',
];

$rulesFixes = [
    'app/Rules/SecurityValidationRules.php' => [
        'App\\Rules\\NoMaliciousContent',
        'App\\Rules\\SecureFileName', 
        'App\\Rules\\StrongPassword'
    ]
];

function fixNamespaceInFile($filePath, $correctNamespace) {
    if (!file_exists($filePath)) {
        echo "Skipping $filePath - file not found\n";
        return;
    }
    
    $content = file_get_contents($filePath);
    
    // Pattern to match namespace declaration
    $pattern = '/^namespace\s+[^;]+;/m';
    
    if (preg_match($pattern, $content)) {
        $newContent = preg_replace($pattern, "namespace $correctNamespace;", $content);
        
        if ($newContent !== $content) {
            file_put_contents($filePath, $newContent);
            echo "Fixed namespace in: $filePath -> $correctNamespace\n";
        } else {
            echo "No change needed for: $filePath\n";
        }
    } else {
        echo "No namespace found in: $filePath\n";
    }
}

function fixClassNameInFile($filePath, $correctClassName) {
    if (!file_exists($filePath)) {
        echo "Skipping $filePath - file not found\n";
        return;
    }
    
    $content = file_get_contents($filePath);
    
    // Get the old class name from the file
    $pattern = '/class\s+(\w+)/';
    if (preg_match($pattern, $content, $matches)) {
        $oldClassName = $matches[1];
        
        if ($oldClassName !== $correctClassName) {
            $newContent = str_replace("class $oldClassName", "class $correctClassName", $content);
            file_put_contents($filePath, $newContent);
            echo "Fixed class name in: $filePath -> $correctClassName\n";
        }
    }
}

echo "Starting PSR-4 autoloading fixes...\n\n";

echo "Fixing Request class namespaces...\n";
foreach ($fixes as $filePath => $namespace) {
    fixNamespaceInFile($filePath, $namespace);
}

echo "\nFixing Repository class namespaces...\n";
foreach ($repositoryFixes as $filePath => $className) {
    fixClassNameInFile($filePath, $className);
}

echo "\nFixing Controller class namespaces...\n";
foreach ($controllerFixes as $filePath => $namespace) {
    fixNamespaceInFile($filePath, $namespace);
}

echo "\nFixing Resource class namespaces...\n";
foreach ($resourceFixes as $filePath => $namespace) {
    fixNamespaceInFile($filePath, $namespace);
}

echo "\nFixing Middleware class namespaces...\n";
foreach ($middlewareFixes as $filePath => $className) {
    fixClassNameInFile($filePath, $className);
}

echo "\nFixing Model class namespaces...\n";
foreach ($modelFixes as $filePath => $namespace) {
    fixNamespaceInFile($filePath, $namespace);
}

echo "\nFixing Command class namespaces...\n";
foreach ($commandFixes as $filePath => $namespace) {
    fixNamespaceInFile($filePath, $namespace);
}

echo "\n✅ PSR-4 autoloading fixes completed!\n";
echo "Run 'composer dump-autoload' to regenerate the autoloader.\n";
?> 