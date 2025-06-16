<?php

return [
    // User related validation messages
    'user' => [
        'first_name_required' => 'The first name field is required.',
        'last_name_required' => 'The last name field is required.',
        'email_required' => 'The email field is required.',
        'email_unique' => 'This email address is already taken.',
        'password_required' => 'The password field is required.',
        'password_min' => 'The password must be at least 8 characters.',
        'password_confirmed' => 'The password confirmation does not match.',
    ],

    // Company related validation messages
    'company' => [
        'name_required' => 'The company name field is required.',
        'name_unique' => 'This company name is already taken.',
        'email_required' => 'The company email field is required.',
        'email_unique' => 'This company email is already taken.',
        'industry_required' => 'Please select an industry.',
        'company_size_required' => 'Please select company size.',
        'website_url' => 'Please enter a valid website URL.',
    ],

    // Job related validation messages
    'job' => [
        'title_required' => 'The job title field is required.',
        'job_category_required' => 'Please select a job category.',
        'job_type_required' => 'Please select a job type.',
        'salary_from_numeric' => 'The salary from must be a number.',
        'salary_to_numeric' => 'The salary to must be a number.',
        'description_required' => 'The job description field is required.',
        'location_required' => 'The job location field is required.',
    ],

    // Candidate related validation messages
    'candidate' => [
        'first_name_required' => 'The first name field is required.',
        'last_name_required' => 'The last name field is required.',
        'email_required' => 'The email field is required.',
        'phone_required' => 'The phone number field is required.',
        'experience_numeric' => 'The experience must be a number.',
        'skills_required' => 'Please select at least one skill.',
    ],

    // General validation messages
    'general' => [
        'name_required' => 'The name field is required.',
        'title_required' => 'The title field is required.',
        'description_required' => 'The description field is required.',
        'status_required' => 'Please select a status.',
        'image_mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
        'image_max' => 'The image may not be greater than 2MB.',
    ],

    // Authentication validation messages
    'auth' => [
        'invalid_credentials' => 'These credentials do not match our records.',
        'account_disabled' => 'Your account has been disabled. Please contact administrator.',
        'email_not_verified' => 'Please verify your email address before proceeding.',
        'token_expired' => 'The verification token has expired.',
        'password_reset_sent' => 'Password reset link has been sent to your email.',
    ],

    // File upload validation messages
    'file' => [
        'resume_required' => 'Please upload your resume.',
        'resume_mimes' => 'Resume must be a PDF, DOC, or DOCX file.',
        'resume_max' => 'Resume file size cannot exceed 5MB.',
        'avatar_mimes' => 'Avatar must be an image file (jpeg, png, jpg, gif).',
        'avatar_max' => 'Avatar file size cannot exceed 2MB.',
    ],
];
