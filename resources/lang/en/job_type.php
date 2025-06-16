<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Job Type Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for job type related functionality
    | throughout the application. Feel free to modify these language lines
    | according to your application's requirements.
    |
    */

    'title' => 'Job Types',
    'singular' => 'Job Type',
    'plural' => 'Job Types',

    // Navigation & Menu
    'menu' => [
        'job_types' => 'Job Types',
        'manage_job_types' => 'Manage Job Types',
        'create_job_type' => 'Create Job Type',
        'edit_job_type' => 'Edit Job Type',
        'job_type_settings' => 'Job Type Settings',
    ],

    // Page Titles
    'pages' => [
        'index' => 'Job Types Management',
        'create' => 'Create New Job Type',
        'edit' => 'Edit Job Type',
        'show' => 'Job Type Details',
        'statistics' => 'Job Type Statistics',
    ],

    // Form Fields
    'fields' => [
        'name' => 'Name',
        'description' => 'Description',
        'slug' => 'Slug',
        'icon' => 'Icon',
        'color' => 'Color',
        'is_default' => 'Default Type',
        'is_active' => 'Active',
        'is_featured' => 'Featured',
        'sort_order' => 'Sort Order',
        'meta_title' => 'Meta Title',
        'meta_description' => 'Meta Description',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'usage_count' => 'Usage Count',
        'jobs_count' => 'Jobs Count',
        'active_jobs_count' => 'Active Jobs Count',
    ],

    // Field Descriptions
    'descriptions' => [
        'name' => 'The display name for this job type',
        'description' => 'A brief description of this job type',
        'slug' => 'URL-friendly version of the name (auto-generated if empty)',
        'icon' => 'Icon name to display with this job type',
        'color' => 'Color code for visual representation',
        'is_default' => 'Mark as a default system job type',
        'is_active' => 'Whether this job type is active and available for use',
        'is_featured' => 'Feature this job type prominently',
        'sort_order' => 'Display order (lower numbers appear first)',
        'meta_title' => 'SEO title for search engines',
        'meta_description' => 'SEO description for search engines',
    ],

    // Placeholders
    'placeholders' => [
        'name' => 'Enter job type name...',
        'description' => 'Describe this job type...',
        'slug' => 'job-type-slug',
        'icon' => 'briefcase',
        'color' => '#3B82F6',
        'sort_order' => '1',
        'meta_title' => 'SEO title...',
        'meta_description' => 'SEO description...',
        'search' => 'Search job types...',
    ],

    // Actions
    'actions' => [
        'create' => 'Create Job Type',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'view' => 'View',
        'save' => 'Save Job Type',
        'cancel' => 'Cancel',
        'back' => 'Back to Job Types',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'feature' => 'Feature',
        'unfeature' => 'Unfeature',
        'bulk_actions' => 'Bulk Actions',
        'export' => 'Export',
        'import' => 'Import',
        'duplicate' => 'Duplicate',
        'search' => 'Search',
        'filter' => 'Filter',
        'sort' => 'Sort',
    ],

    // Status Labels
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'default' => 'Default',
        'custom' => 'Custom',
        'featured' => 'Featured',
        'not_featured' => 'Not Featured',
    ],

    // Success Messages
    'messages' => [
        'created_successfully' => 'Job type created successfully.',
        'updated_successfully' => 'Job type updated successfully.',
        'deleted_successfully' => 'Job type deleted successfully.',
        'activated_successfully' => 'Job type activated successfully.',
        'deactivated_successfully' => 'Job type deactivated successfully.',
        'featured_successfully' => 'Job type featured successfully.',
        'unfeatured_successfully' => 'Job type unfeatured successfully.',
        'bulk_updated' => ':count job types updated successfully.',
        'exported_successfully' => 'Job types exported successfully.',
        'imported_successfully' => ':count job types imported successfully.',
        'duplicated_successfully' => 'Job type duplicated successfully.',
        'no_changes' => 'No changes were made.',
    ],

    // Error Messages
    'errors' => [
        'not_found' => 'Job type not found.',
        'cannot_delete_in_use' => 'Cannot delete job type that is currently in use by jobs.',
        'cannot_deactivate_default' => 'Cannot deactivate default job types.',
        'duplicate_name' => 'A job type with this name already exists.',
        'duplicate_slug' => 'A job type with this slug already exists.',
        'invalid_color' => 'Please enter a valid color code.',
        'invalid_icon' => 'Please enter a valid icon name.',
        'max_featured_reached' => 'Maximum number of featured job types reached.',
        'creation_failed' => 'Failed to create job type.',
        'update_failed' => 'Failed to update job type.',
        'deletion_failed' => 'Failed to delete job type.',
        'unauthorized' => 'You are not authorized to perform this action.',
        'validation_failed' => 'Validation failed. Please check your input.',
    ],

    // Filters
    'filters' => [
        'all' => 'All Job Types',
        'active' => 'Active Only',
        'inactive' => 'Inactive Only',
        'default' => 'Default Types',
        'custom' => 'Custom Types',
        'featured' => 'Featured Only',
        'with_jobs' => 'With Jobs',
        'without_jobs' => 'Without Jobs',
        'high_demand' => 'High Demand',
        'low_usage' => 'Low Usage',
    ],

    // Sorting Options
    'sorting' => [
        'name_asc' => 'Name (A-Z)',
        'name_desc' => 'Name (Z-A)',
        'created_newest' => 'Newest First',
        'created_oldest' => 'Oldest First',
        'updated_newest' => 'Recently Updated',
        'most_popular' => 'Most Popular',
        'least_popular' => 'Least Popular',
        'sort_order' => 'Sort Order',
        'usage_high' => 'High Usage',
        'usage_low' => 'Low Usage',
    ],

    // Statistics
    'statistics' => [
        'total_job_types' => 'Total Job Types',
        'active_job_types' => 'Active Job Types',
        'inactive_job_types' => 'Inactive Job Types',
        'default_job_types' => 'Default Job Types',
        'custom_job_types' => 'Custom Job Types',
        'featured_job_types' => 'Featured Job Types',
        'with_jobs' => 'With Jobs',
        'without_jobs' => 'Without Jobs',
        'average_usage' => 'Average Usage',
        'total_jobs' => 'Total Jobs',
        'most_popular' => 'Most Popular',
        'trending' => 'Trending',
        'recent_additions' => 'Recent Additions',
    ],

    // Demand Levels
    'demand' => [
        'high_demand' => 'High Demand',
        'medium_demand' => 'Medium Demand',
        'low_demand' => 'Low Demand',
        'minimal_demand' => 'Minimal Demand',
    ],

    // Job Type Categories
    'categories' => [
        'employment_type' => 'Employment Type',
        'work_arrangement' => 'Work Arrangement',
        'contract_type' => 'Contract Type',
        'experience_level' => 'Experience Level',
        'duration' => 'Duration',
        'commitment' => 'Commitment Level',
    ],

    // Common Job Types
    'types' => [
        'full_time' => 'Full-Time',
        'part_time' => 'Part-Time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
        'freelance' => 'Freelance',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
        'on_site' => 'On-Site',
        'consultant' => 'Consultant',
        'volunteer' => 'Volunteer',
        'seasonal' => 'Seasonal',
    ],

    // Confirmations
    'confirmations' => [
        'delete' => 'Are you sure you want to delete this job type?',
        'delete_with_jobs' => 'This job type is used by :count jobs. Deleting it will affect these jobs. Are you sure?',
        'deactivate' => 'Are you sure you want to deactivate this job type?',
        'bulk_delete' => 'Are you sure you want to delete the selected job types?',
        'bulk_action' => 'Are you sure you want to perform this action on the selected job types?',
    ],

    // Table Headers
    'table' => [
        'name' => 'Name',
        'description' => 'Description',
        'status' => 'Status',
        'jobs_count' => 'Jobs',
        'created_at' => 'Created',
        'actions' => 'Actions',
        'icon' => 'Icon',
        'color' => 'Color',
        'sort_order' => 'Order',
        'featured' => 'Featured',
        'default' => 'Default',
    ],

    // Badges
    'badges' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'default' => 'Default',
        'featured' => 'Featured',
        'high_demand' => 'High Demand',
        'new' => 'New',
        'trending' => 'Trending',
    ],

    // Tooltips
    'tooltips' => [
        'default_type' => 'This is a default system job type',
        'featured_type' => 'This job type is featured prominently',
        'high_demand' => 'This job type is in high demand',
        'inactive_type' => 'This job type is currently inactive',
        'has_jobs' => 'This job type has active jobs',
        'no_jobs' => 'This job type has no jobs yet',
    ],

    // Empty States
    'empty' => [
        'no_job_types' => 'No job types found.',
        'no_active_job_types' => 'No active job types found.',
        'no_search_results' => 'No job types match your search criteria.',
        'no_featured_types' => 'No featured job types found.',
        'create_first_type' => 'Create your first job type to get started.',
    ],

    // Pagination
    'pagination' => [
        'showing' => 'Showing :first to :last of :total job types',
        'per_page' => 'job types per page',
    ],

    // Validation Messages (used in requests)
    'validation' => [
        'name' => [
            'required' => 'Job type name is required.',
            'string' => 'Job type name must be a string.',
            'max' => 'Job type name cannot exceed 255 characters.',
            'unique' => 'A job type with this name already exists.',
        ],
        'description' => [
            'string' => 'Description must be a string.',
            'max' => 'Description cannot exceed 1000 characters.',
        ],
        'slug' => [
            'string' => 'Slug must be a string.',
            'max' => 'Slug cannot exceed 255 characters.',
            'unique' => 'A job type with this slug already exists.',
            'regex' => 'Slug can only contain lowercase letters, numbers, and hyphens.',
        ],
        'is_default' => [
            'boolean' => 'Default type field must be true or false.',
        ],
        'is_active' => [
            'boolean' => 'Active field must be true or false.',
        ],
        'is_featured' => [
            'boolean' => 'Featured field must be true or false.',
        ],
        'sort_order' => [
            'integer' => 'Sort order must be a number.',
            'min' => 'Sort order must be at least 0.',
            'max' => 'Sort order cannot exceed 999999.',
        ],
        'icon' => [
            'string' => 'Icon must be a string.',
            'max' => 'Icon name cannot exceed 100 characters.',
            'regex' => 'Icon name can only contain letters, numbers, hyphens, and underscores.',
        ],
        'color' => [
            'string' => 'Color must be a string.',
            'regex' => 'Color must be a valid hex color code (e.g., #FF0000).',
        ],
        'meta_title' => [
            'string' => 'Meta title must be a string.',
            'max' => 'Meta title cannot exceed 60 characters.',
        ],
        'meta_description' => [
            'string' => 'Meta description must be a string.',
            'max' => 'Meta description cannot exceed 160 characters.',
        ],
    ],
];
