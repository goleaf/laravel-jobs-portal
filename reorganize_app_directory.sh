#!/bin/bash

# Script to reorganize the app directory for better structure

echo "Starting reorganization of app directory..."

# Create necessary subdirectories if they don't exist
mkdir -p app/Http/Controllers/User
mkdir -p app/Http/Controllers/Settings
mkdir -p app/Http/Controllers/Content
mkdir -p app/Http/Controllers/MasterData
mkdir -p app/Http/Controllers/Location
mkdir -p app/Http/Requests/User
mkdir -p app/Http/Requests/Settings
mkdir -p app/Http/Requests/Content
mkdir -p app/Http/Resources/Job

# Function to update namespace in a file
function update_namespace {
    local file=$1
    local new_namespace=$2
    sed -i "s|namespace App\\Http\\Controllers;|namespace $new_namespace;|" "$file"
    sed -i "s|namespace App\\Http\\Resources;|namespace $new_namespace;|" "$file"
    sed -i "s|namespace App\\Http\\Requests;|namespace $new_namespace;|" "$file"
}

# Move Controllers to appropriate subdirectories and update namespaces
# User-related controllers
mv app/Http/Controllers/UserController.php app/Http/Controllers/User/UserController.php
update_namespace app/Http/Controllers/User/UserController.php "App\\Http\\Controllers\\User"
mv app/Http/Controllers/CandidateController.php app/Http/Controllers/User/CandidateController.php
update_namespace app/Http/Controllers/User/CandidateController.php "App\\Http\\Controllers\\User"
mv app/Http/Controllers/EmployerController.php app/Http/Controllers/User/EmployerController.php
update_namespace app/Http/Controllers/User/EmployerController.php "App\\Http\\Controllers\\User"

# Settings-related controllers
mv app/Http/Controllers/SettingController.php app/Http/Controllers/Settings/SettingController.php
update_namespace app/Http/Controllers/Settings/SettingController.php "App\\Http\\Controllers\\Settings"
mv app/Http/Controllers/LanguageController.php app/Http/Controllers/Settings/LanguageController.php
update_namespace app/Http/Controllers/Settings/LanguageController.php "App\\Http\\Controllers\\Settings"
mv app/Http/Controllers/LocaleController.php app/Http/Controllers/Settings/LocaleController.php
update_namespace app/Http/Controllers/Settings/LocaleController.php "App\\Http\\Controllers\\Settings"

# Content-related controllers
mv app/Http/Controllers/PostController.php app/Http/Controllers/Content/PostController.php
update_namespace app/Http/Controllers/Content/PostController.php "App\\Http\\Controllers\\Content"
mv app/Http/Controllers/PostCategoryController.php app/Http/Controllers/Content/PostCategoryController.php
update_namespace app/Http/Controllers/Content/PostCategoryController.php "App\\Http\\Controllers\\Content"

# MasterData-related controllers
mv app/Http/Controllers/CareerLevelController.php app/Http/Controllers/MasterData/CareerLevelController.php
update_namespace app/Http/Controllers/MasterData/CareerLevelController.php "App\\Http\\Controllers\\MasterData"
mv app/Http/Controllers/CompanySizeController.php app/Http/Controllers/MasterData/CompanySizeController.php
update_namespace app/Http/Controllers/MasterData/CompanySizeController.php "App\\Http\\Controllers\\MasterData"

# Location-related controllers
mv app/Http/Controllers/CountryController.php app/Http/Controllers/Location/CountryController.php
update_namespace app/Http/Controllers/Location/CountryController.php "App\\Http\\Controllers\\Location"
mv app/Http/Controllers/StateController.php app/Http/Controllers/Location/StateController.php
update_namespace app/Http/Controllers/Location/StateController.php "App\\Http\\Controllers\\Location"

# Move Resources
mv app/Http/Resources/JobResource.php app/Http/Resources/Job/JobResource.php
update_namespace app/Http/Resources/Job/JobResource.php "App\\Http\\Resources\\Job"

# Move Requests (example for a few files)
mv app/Http/Requests/ChangePasswordRequest.php app/Http/Requests/User/ChangePasswordRequest.php
update_namespace app/Http/Requests/User/ChangePasswordRequest.php "App\\Http\\Requests\\User"
mv app/Http/Requests/StorePostRequest.php app/Http/Requests/Content/StorePostRequest.php
update_namespace app/Http/Requests/Content/StorePostRequest.php "App\\Http\\Requests\\Content"

# TODO: Add more mv and update_namespace commands for other files

echo "Reorganization complete. Please check the moved files and update route definitions if necessary."

echo "Running a grep search to identify potentially unused files..."
# Search for files not referenced in the codebase (basic check, may need refinement)
find app -type f -name "*.php" | while read -r file; do
    filename=$(basename "$file")
    if ! grep -r "$filename" app routes resources --exclude="$file" >/dev/null; then
        echo "Potentially unused file: $file"
    fi
done

echo "Script execution finished. Review the output for unused files and verify the reorganization." 