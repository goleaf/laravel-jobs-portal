# CONTEXT7 CRITICAL ROUTE & SECURITY FIXES

Generated: 2025-06-06 07:08:32

## Issues Fixed

### 🛣️ Route Issues
- Fixed incomplete route `admin.` → `admin.candidates.create`
- Fixed broken export route reference
- Added missing dashboard routes for all user types

### 🔒 Security Issues
- Fixed XSS vulnerabilities by converting `{!! !!}` to `{{ }}`
- Secured form output rendering
- Protected URL generation calls

### 🎨 CSS Issues
- Fixed broken TailwindCSS class combinations
- Cleaned up migration artifacts
- Standardized button styling

## Statistics

- Files Checked: 934
- Routes Fixed: 1
- Security Fixes: 13
- Syntax Fixes: 360

## Files Modified

### resources/views/candidates/table-components/add_button.blade.php
- **Fix**: Fixed incomplete routes
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/edit_modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/add_modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/job_categories/add_modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/job_types/edit_modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/job_types/add_modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/vendor/flash/modal.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/button.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/input.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/select.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/checkbox.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/radio.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/components/submit-button.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### resources/views/shared/components/table/index.blade.php
- **Fix**: Fixed security vulnerabilities
- **Time**: 2025-06-06 07:08:32

### routes/web.php
- **Fix**: Added critical missing routes
- **Time**: 2025-06-06 07:08:32

### resources/views/required_degree_levels/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/required_degree_levels/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/required_degree_levels/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/required_degree_levels/required_degree_level_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/required_degree_levels/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/welcome.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/table-components/email_verified.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/table-components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/career_levels.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/cities.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/languages.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/skills.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/industries.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/countries.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/marital_status.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/functional_areas.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/modals/states.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/candidate_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidates/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/applied_job/show_applied_jobs_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/applied_job/schedule_slot_book.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/dashboard/dashboard.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/resume_table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/resume_table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career-informations.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/edit_education_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/add_education_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/cv_preview_model.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/upload_resume_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/add_experience_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/modals/edit_experience_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/cv-builder.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/general.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/create_education.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/edit_general.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/show_experience.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/edit_online_profile.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/show_education.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/show_online_profile.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/create_experience.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/edit_education.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/profile/career_informations/edit_experience.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/layouts/header.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/reported_candidate/table-components/candidate_firstname.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/reported_candidate/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/reported_candidate/reported_candidate_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/reported_candidate/reported_candidate_show_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/favourite_companies/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/job_alert/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/job_alert/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate/favourite_jobs/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/settings/about_us.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/settings/env_setting.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/settings/front_office_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/settings/social_settings.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/settings/general.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/image_sliders/image_slider_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blogs/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_notification/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_notification/send_notification.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_expired/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blog_categories/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blog_categories/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blog_categories/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blog_categories/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/blog_categories/post_category_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/branding_sliders/branding_slider_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/plans/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/marital_status/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/marital_status/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/marital_status/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/marital_status/marital_status_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/marital_status/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/candidate/candidate_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/candidate/report_to_candidate_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/home/home.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/blogs/blogs_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/auth/candidate_login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/auth/candidate_register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/auth/employer_login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/auth/employer_register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/common/job_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/layouts/header.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/jobs/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/jobs/apply_job/apply_job.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/jobs/job_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/jobs/report_job_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/jobs/email_to_friend.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/company/report_to_company_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web/company/company_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/all_role_buttons.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/passwords/confirm.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/passwords/email.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/verify.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth/admin_login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/company_sizes/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/company_sizes/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/company_sizes/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/company_sizes/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/company_sizes/company_size_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/functional_areas/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/functional_areas/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/functional_areas/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/functional_areas/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/functional_areas/functional_area_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_stages/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_stages/job_stages_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_stages/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_stages/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_stages/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/dashboard/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/transactions/table_components/invoice.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/layouts/header.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/reported_job_table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/reportedJobs_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/job_table_components/status.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/job_table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/job_table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/job_table_components/featured_job.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/job_table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/jobs/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/job_stages_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/table_components/edit_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/add_batch_slot_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/edit_batch_slot_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/schedule_interview_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/job_applications/view_slot_screen.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/companies/reported_company_table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/companies/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/companies/reported_employee_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer/companies/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_currencies/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_currencies/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_currencies/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_currencies/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_periods/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_periods/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_periods/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_periods/salary_period_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/salary_periods/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/career_levels/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/career_levels/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/career_levels/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/career_levels/career_level_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/career_levels/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/translation-manager/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/translation-manager/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/email_templates/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/email_templates/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/post_comments/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/industries/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/industries/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/industries/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/industries/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/industries/industry_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer_profile/edit_profile_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/employer_profile/change_password_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admins/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/candidate/candidate_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/candidate/report_to_candidate_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/home/home.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/blogs/blogs_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/auth/candidate_login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/auth/candidate_register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/auth/employer_login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/auth/employer_register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/auth/login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/common/job_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/layouts/header.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/jobs/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/jobs/job_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/jobs/report_job_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/jobs/email_to_friend.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_web_template/company/company_details.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_categories/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_categories/table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_categories/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_categories/job_categories_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/languages/language_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/faqs/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/faqs/faq_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/faqs/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/faqs/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/faqs/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/dashboard/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/notification_settings/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/transactions/failed_payments.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/transactions/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/applied-jobs.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/candidate-search.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/company-search.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/favourite-companies.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/view-slot-screen.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/components/simple-table.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/components/filters/multiselect.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/components/filters/daterange.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/components/filters/numberrange.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/livewire/job-type-table.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/layouts/auth.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/layouts/header.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/layouts/sidebar.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/layouts/simple.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/table-components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cities/city_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/all_role_buttons.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/register.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/passwords/confirm.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/passwords/email.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/verify.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/auth_template/login.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/noticeboards/noticeboard_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/noticeboards/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/noticeboards/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/noticeboards/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/noticeboards/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/front_settings/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/state_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/table-components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/states/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/testimonial/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/testimonial/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/testimonial/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/testimonial/testimonial-card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/testimonial/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_shifts/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_shifts/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_shifts/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_shifts/job_shifts_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_shifts/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/skills/skill_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/skills/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/skills/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/ownership_types/ownership_type_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/ownership_types/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/ownership_types/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/ownership_types/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/ownership_types/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/inquires/table-components/action_buttons.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/inquires/inquiry_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/inquires/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/subscribers/subscriber_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/subscribers/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_tags/job_tags_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_tags/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_tags/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_tags/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_tags/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/errors/404.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/table-components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/job_shifts.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/career_levels.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/job_category.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/job_type.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/cities.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/salary_periods.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/skills.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/required_degree_levels.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/job_tags.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/countries.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/functional_areas.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/modals/states.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/jobs/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/countries/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/countries/table-components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/countries/table-components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/countries/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/countries/country_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/cms_services/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job_types/job_type_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/selected_candidate/table-components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/edit_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/header_sliders_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/header_sliders/add_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/pricing/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/pricing/cancel_subscription_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/pricing/payment_methods.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/table_components/email_verified.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/table_components/filter.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/table_components/action_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/table_components/is_featured.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/table_components/add_button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/company_sizes.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/cities.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/industries.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/countries.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/ownership_types.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/modals/states.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/edit.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/create.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/companies_card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/show.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/companies/edit_fields.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate_profile/edit_profile_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/candidate_profile/change_password_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/vendor/flash/modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/user_profile/edit_profile_modal.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/components/form-button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/components/icon-documentation.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/components/language-switcher.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/components/realtime-dashboard.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/components/job-card.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/job-types/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/about.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/contact.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admin/candidates/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admin/jobs/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admin/transactions/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/admin/dashboard/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/frontend/home/index.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

### resources/views/shared/components/forms/button.blade.php
- **Fix**: Fixed broken CSS classes
- **Time**: 2025-06-06 07:08:32

