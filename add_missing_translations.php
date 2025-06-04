<?php

/**
 * Add Missing Translation Keys
 * Adds all missing translation keys identified by the blade template validator
 */

echo "🌐 Adding Missing Translation Keys\n";
echo "=" . str_repeat("=", 40) . "\n\n";

// Load existing translations
$enJsonPath = 'lang/en.json';
$translations = [];

if (file_exists($enJsonPath)) {
    $translations = json_decode(file_get_contents($enJsonPath), true) ?: [];
    echo "📚 Loaded " . count($translations) . " existing translation keys\n\n";
}

// Missing translation keys that need to be added
$missingKeys = [
    // Common translations
    'web.common.search' => 'Search',
    'messages.common.search' => 'Search',
    'messages.common.filter' => 'Filter',
    'messages.common.action' => 'Action',
    'messages.common.actions' => 'Actions',
    'messages.common.asc' => 'Ascending',
    'messages.common.desc' => 'Descending',
    'messages.common.add' => 'Add',
    'messages.common.edit' => 'Edit',
    'messages.common.view' => 'View',
    'messages.common.delete' => 'Delete',
    'messages.common.save' => 'Save',
    'messages.common.cancel' => 'Cancel',
    'messages.common.process' => 'Processing...',
    'messages.common.processing' => 'Processing...',
    'messages.common.discard' => 'Discard',
    'messages.common.status' => 'Status',
    'messages.common.name' => 'Name',
    'messages.common.email' => 'Email',
    'messages.common.phone' => 'Phone',
    'messages.common.description' => 'Description',
    'messages.common.created_on' => 'Created On',
    'messages.common.n/a' => 'N/A',
    'messages.common.note' => 'Note',
    'messages.common.reason' => 'Reason',
    'messages.common.download' => 'Download',
    'messages.common.no_records_found' => 'No records found',
    'messages.common.showing' => 'Showing',
    'messages.common.to' => 'to',
    'messages.common.of' => 'of',
    'messages.common.results' => 'results',
    'messages.common.per_page' => 'per page',
    'messages.common.default_country_code' => 'Default Country Code',
    'messages.common.default_language' => 'Default Language',
    'messages.common.select_language' => 'Select Language',
    
    // Navigation & Menu
    'messages.general' => 'General',
    'messages.footer_settings' => 'Footer Settings',
    'messages.social_settings' => 'Social Settings',
    'messages.about_us' => 'About Us',
    'messages.env' => 'Environment Settings',
    'messages.settings' => 'Settings',
    
    // States
    'messages.state.new_state' => 'New State',
    'messages.state.edit_state' => 'Edit State',
    'messages.state.states' => 'States',
    'messages.state.state_name' => 'State Name',
    'messages.state.country_name' => 'Country Name',
    'messages.state.no_state_found' => 'No states found',
    'messages.state.no_state_available' => 'No states available',
    
    // Subscribers
    'messages.subscribers' => 'Subscribers',
    'messages.no_subscriber_found' => 'No subscribers found',
    'messages.no_subscriber_available' => 'No subscribers available',
    
    // Skills
    'messages.skills' => 'Skills',
    'messages.skill.new_skill' => 'New Skill',
    'messages.skill.edit_skill' => 'Edit Skill',
    'messages.skill.skill_detail' => 'Skill Details',
    'messages.skill.name' => 'Skill Name',
    'messages.skill.description' => 'Description',
    
    // Job Categories
    'messages.job_categories' => 'Job Categories',
    
    // Companies
    'messages.company.select_country' => 'Select Country',
    'messages.company.select_currency' => 'Select Currency',
    'messages.company.current_password' => 'Current Password',
    'messages.company.new_password' => 'New Password',
    'messages.company.confirm_password' => 'Confirm Password',
    'messages.company.reported_by' => 'Reported By',
    'messages.company.reported_on' => 'Reported On',
    'messages.company.notes' => 'Notes',
    
    // Users & Candidates
    'messages.user.user_name' => 'User Name',
    'messages.user.change_password' => 'Change Password',
    'messages.user.edit_profile' => 'Edit Profile',
    'messages.candidate.first_name' => 'First Name',
    'messages.candidate.last_name' => 'Last Name',
    'messages.candidate.email' => 'Email',
    'messages.candidate.phone' => 'Phone',
    'messages.candidate.profile' => 'Profile Picture',
    'messages.candidate.reported_candidate_detail' => 'Reported Candidate Details',
    
    // Phone validation
    'messages.phone.valid_number' => 'Please enter a valid phone number',
    'messages.phone.invalid_number' => 'Invalid phone number format',
    
    // Marital Status
    'messages.marital_statuses' => 'Marital Statuses',
    'messages.marital_status.new_marital_status' => 'New Marital Status',
    'messages.marital_status.edit_marital_status' => 'Edit Marital Status',
    'messages.marital_status.marital_status_detail' => 'Marital Status Details',
    'messages.marital_status.marital_status' => 'Marital Status',
    'messages.marital_status.description' => 'Description',
    
    // Noticeboards
    'messages.noticeboards' => 'Noticeboards',
    'messages.noticeboard.new_noticeboard' => 'New Noticeboard',
    'messages.noticeboard.edit_noticeboard' => 'Edit Noticeboard',
    'messages.noticeboard.noticeboard_detail' => 'Noticeboard Details',
    'messages.noticeboard.title' => 'Title',
    'messages.noticeboard.description' => 'Description',
    
    // Ownership Types
    'messages.ownership_types' => 'Ownership Types',
    'messages.ownership_type.new_ownership_type' => 'New Ownership Type',
    'messages.ownership_type.edit_ownership_type' => 'Edit Ownership Type',
    'messages.ownership_type.ownership_type_detail' => 'Ownership Type Details',
    
    // Plans & Subscriptions
    'messages.subscriptions_plans' => 'Subscription Plans',
    'messages.plan.new_subscription_plan' => 'New Subscription Plan',
    'messages.plan.edit_subscription_plan' => 'Edit Subscription Plan',
    'messages.plan.allowed_jobs' => 'Allowed Jobs',
    'messages.plan.currency' => 'Currency',
    'messages.plan.amount' => 'Amount',
    'messages.plan.per_month' => 'per month',
    'messages.plan.ends_at' => 'Ends At',
    'messages.plan.renews_on' => 'Renews On',
    'messages.plan.jobs_allowed' => 'Jobs Allowed',
    'messages.plan.job_allowed' => 'Job Allowed',
    'messages.plan.is_trial_plan' => 'Trial Plan',
    'messages.plan.jobs_used' => 'Jobs Used',
    'messages.plan.job_used' => 'Job Used',
    'messages.plan.upgrade' => 'Upgrade',
    'messages.plan.current_plan' => 'Current Plan',
    'messages.plan.subscription_cancelled' => 'Subscription Cancelled',
    'messages.plan.cancel_subscription' => 'Cancel Subscription',
    'messages.plan.cancel_reason' => 'Cancellation Reason',
    'messages.plan.processing' => 'Processing...',
    'messages.plan.purchase' => 'Purchase',
    'messages.plan.pay_with_stripe' => 'Pay with Stripe',
    'messages.plan.pay_with_paypal' => 'Pay with PayPal',
    'messages.plan.pay_with_manually' => 'Pay Manually',
    'messages.plan.pay_with_stack' => 'Pay with Stack',
    
    // Required Degree Levels
    'messages.required_degree_levels' => 'Required Degree Levels',
    'messages.required_degree_level.new_required_degree_level' => 'New Required Degree Level',
    'messages.required_degree_level.edit_required_degree_level' => 'Edit Required Degree Level',
    'messages.required_degree_level.name' => 'Degree Level Name',
    
    // Salary
    'messages.salary_currencies' => 'Salary Currencies',
    'messages.salary_currency.new_salary_currency' => 'New Salary Currency',
    'messages.salary_currency.edit_salary_currency' => 'Edit Salary Currency',
    'messages.salary_currency.currency_name' => 'Currency Name',
    'messages.salary_currency.currency_icon' => 'Currency Icon',
    'messages.salary_currency.currency_code' => 'Currency Code',
    
    'messages.salary_periods' => 'Salary Periods',
    'messages.salary_period.new_salary_period' => 'New Salary Period',
    'messages.salary_period.edit_salary_period' => 'Edit Salary Period',
    'messages.salary_period.salary_period_detail' => 'Salary Period Details',
    'messages.salary_period.period' => 'Period',
    'messages.salary_period.description' => 'Description',
    
    // Selected Candidates
    'messages.selected_candidate' => 'Selected Candidates',
    
    // Settings
    'messages.setting.notification_settings' => 'Notification Settings',
    'messages.setting.general' => 'General Settings',
    'messages.setting.application_name' => 'Application Name',
    'messages.setting.company_url' => 'Company URL',
    'messages.setting.company_description' => 'Company Description',
    'messages.setting.logo' => 'Logo',
    'messages.setting.choose' => 'Choose File',
    'messages.setting.image_validation' => 'Allowed file types: png, jpg, jpeg.',
    'messages.setting.favicon' => 'Favicon',
    'messages.setting.enable_google_recaptcha' => 'Enable Google reCAPTCHA',
    'messages.setting.address' => 'Address',
    'messages.setting.phone' => 'Phone',
    'messages.setting.email' => 'Email',
    'messages.setting.facebook_url' => 'Facebook URL',
    'messages.setting.twitter_url' => 'Twitter URL',
    'messages.setting.google_plus_url' => 'Google Plus URL',
    'messages.setting.linkedIn_url' => 'LinkedIn URL',
    'messages.setting.privacy_policy' => 'Privacy Policy',
    'messages.setting.terms_conditions' => 'Terms & Conditions',
    'messages.setting.enable_edit' => 'Enable Edit',
    'messages.setting.disable_edit' => 'Disable Edit',
    'messages.setting.enable_cookie' => 'Enable Cookie',
    'messages.setting.disable_cookie' => 'Disable Cookie',
    
    // Environment Settings
    'messages.setting.facebook' => 'Facebook Settings',
    'messages.setting.facebook_app_id' => 'Facebook App ID',
    'messages.setting.facebook_app_secret' => 'Facebook App Secret',
    'messages.setting.facebook_redirect' => 'Facebook Redirect URL',
    'messages.setting.pusher' => 'Pusher Settings',
    'messages.setting.pusher_app_id' => 'Pusher App ID',
    'messages.setting.pusher_app_key' => 'Pusher App Key',
    'messages.setting.pusher_app_secret' => 'Pusher App Secret',
    'messages.setting.pusher_app_cluster' => 'Pusher App Cluster',
    'messages.setting.stripe' => 'Stripe Settings',
    'messages.setting.stripe_key' => 'Stripe Key',
    'messages.setting.stripe_secret_key' => 'Stripe Secret Key',
    'messages.setting.stripe_webhook_key' => 'Stripe Webhook Key',
    'messages.setting.paypal' => 'PayPal Settings',
    'messages.setting.paypal_client_id' => 'PayPal Client ID',
    'messages.setting.paypal_secret' => 'PayPal Secret',
    'messages.setting.paystack' => 'Paystack Settings',
    'messages.setting.paystack_key' => 'Paystack Key',
    'messages.setting.paystack_secret' => 'Paystack Secret',
    'messages.setting.paystack_payment_url' => 'Paystack Payment URL',
    'messages.setting.linkedin' => 'LinkedIn Settings',
    'messages.setting.linkedin_client_id' => 'LinkedIn Client ID',
    'messages.setting.linkedin_client_secret' => 'LinkedIn Client Secret',
    'messages.setting.google' => 'Google Settings',
    'messages.setting.google_client_id' => 'Google Client ID',
    'messages.setting.google_client_secret' => 'Google Client Secret',
    'messages.setting.google_redirect' => 'Google Redirect URL',
    'messages.setting.cookie' => 'Cookie Settings',
    
    // Testimonials
    'messages.testimonial.testimonials' => 'Testimonials',
    'messages.testimonial.new_testimonial' => 'New Testimonial',
    'messages.testimonial.edit_testimonial' => 'Edit Testimonial',
    'messages.testimonial.testimonial_detail' => 'Testimonial Details',
    'messages.testimonial.customer_name' => 'Customer Name',
    'messages.testimonial.customer_image' => 'Customer Image',
    'messages.testimonial.description' => 'Description',
    'messages.testimonial.no_testimonial_found' => 'No testimonials found',
    'messages.testimonial.no_testimonial_available' => 'No testimonials available',
    
    // Transactions
    'messages.transactions' => 'Transactions',
    'messages.flash.payment_failed_try_again' => 'Payment failed. Please try again.',
    'messages.see_all_plans' => 'See All Plans',
    
    // Translation Manager
    'messages.translation_manager' => 'Translation Manager',
    'messages.language.new_language' => 'New Language',
    
    // Job Stages
    'messages.job_stage.batch' => 'Batch',
    'messages.job_stage.date' => 'Date',
    'messages.job_stage.time' => 'Time',
    'messages.job_stage.candidate_note' => 'Candidate Note',
    'messages.job_stage.your_note' => 'Your Note',
    'messages.job_stage.cancel_slot' => 'Cancel Slot',
    'messages.job_stage.cancel_this_slot' => 'Cancel this slot',
    'messages.job_stage.no_slot_available' => 'No slots available',
    
    // Jobs
    'messages.job.notes' => 'Notes',
    'messages.all_resumes' => 'All Resumes',
    
    // Posts & Comments
    'messages.post.comment' => 'Comment',
    'messages.post.post' => 'Post',
    'messages.post.image' => 'Image',
    'messages.post_comment.post_comment_details' => 'Post Comment Details',
    
    // Job Applications
    'messages.job_application.candidate_name' => 'Candidate Name',
    
    // FAQ
    'messages.faq.title' => 'Title',
    
    // Flash Messages
    'messages.flash.enter_notes' => 'Please enter your notes',
    'messages.flash.enter_cancel_reason' => 'Please enter cancellation reason',
    
    // User Language
    'messages.user_language.change_language' => 'Change Language',
    'messages.user_language.language' => 'Language',
    
    // Tooltips
    'messages.tooltip.change_app_logo' => 'Click to change logo',
    'messages.tooltip.change_favicon' => 'Click to change favicon',
    'messages.tooltip.change_image' => 'Click to change image',
    'messages.tooltip.change_profile' => 'Click to change profile picture',
    
    // Inquiries
    'messages.inquiry.name' => 'Name',
    
    // Employer Menu
    'messages.employer_menu.manage_subscriptions' => 'Manage Subscriptions',
    
    // App Footer
    'messages.app_footer_logo' => 'Footer Logo',
    
    // Table Component
    'Search...' => 'Search...',
    'Per Page' => 'Per Page',
    'Filter by' => 'Filter by',
    'All' => 'All',
    'From' => 'From',
    'To' => 'To',
    'Reset' => 'Reset',
    'Remove filter' => 'Remove filter',
    'Actions' => 'Actions',
    'No records found.' => 'No records found.',
    
    // Common date/time
    'messages.common.you_cancel_slot_date' => 'You are cancelling the slot for',
    'messages.common.and_time' => 'and time',
];

// Add missing keys to translations
$addedCount = 0;
foreach ($missingKeys as $key => $value) {
    if (!isset($translations[$key])) {
        $translations[$key] = $value;
        $addedCount++;
    }
}

// Save updated translations
file_put_contents($enJsonPath, json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "✅ Added $addedCount new translation keys\n";
echo "📊 Total translation keys: " . count($translations) . "\n";
echo "🌐 Updated: lang/en.json\n\n";

// Run blade template validation again to check remaining issues
echo "🔍 Running validation again to check for remaining issues...\n";
$output = shell_exec('php validate_blade_templates.php 2>&1');

// Count remaining warnings
$warningCount = substr_count($output, 'not found');
$componentWarnings = substr_count($output, 'might not exist');

echo "📊 Validation Results:\n";
echo "   Missing translation keys: $warningCount\n";
echo "   Missing components: $componentWarnings\n";

if ($warningCount < 100) {
    echo "✅ Significant improvement in translation coverage!\n";
} else {
    echo "⚠️ Still some translation keys missing - may need manual review\n";
}

echo "\n🎉 Missing translation keys addition complete!\n"; 