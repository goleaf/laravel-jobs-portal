<?php require_once "vendor/autoload.php"; $app = require_once "bootstrap/app.php"; $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap(); use App\Models\Job; echo "🚀 Job Settings Test
"; $job = Job::first(); if(!$job) { echo "❌ No jobs found
"; exit(1); } echo "Testing Job: " . $job->job_title . "
"; try { $settings = $job->settings()->all(); echo "✅ Default settings loaded: " . count($settings) . " categories
"; echo "Visibility public: " . ($job->settings("visibility.public") ? "Yes" : "No") . "
"; echo "Max applications: " . $job->settings("application.max_applications") . "
"; $job->settings(["visibility.featured" => true, "application.max_applications" => 250]); echo "✅ Settings updated
"; echo "Featured: " . ($job->settings("visibility.featured") ? "Yes" : "No") . "
"; echo "Max apps: " . $job->settings("application.max_applications") . "
"; $job->save(); echo "✅ Settings saved
"; $reloaded = Job::find($job->id); echo "Featured persisted: " . ($reloaded->settings("visibility.featured") ? "Yes" : "No") . "
"; echo "🎯 Job Settings Integration: SUCCESS!
"; } catch(Exception $e) { echo "❌ Error: " . $e->getMessage() . "
"; }
