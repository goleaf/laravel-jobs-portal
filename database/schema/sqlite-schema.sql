CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "countries"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "short_code" varchar not null,
  "phone_code" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "iso_code" varchar,
  "currency" varchar,
  "is_active" tinyint(1) not null default '1',
  "is_default" tinyint(1) not null default '0',
  "is_featured" tinyint(1) not null default '0',
  "flag_url" varchar,
  "region" varchar,
  "continent" varchar,
  "population" integer,
  "area_km2" float,
  "capital" varchar,
  "timezone" varchar,
  "languages" text,
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "states"(
  "id" integer primary key autoincrement not null,
  "country_id" integer not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  foreign key("country_id") references "countries"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "first_name" varchar not null,
  "last_name" varchar,
  "email" varchar not null,
  "phone" varchar,
  "email_verified_at" datetime,
  "password" varchar not null,
  "dob" date,
  "gender" integer,
  "country_id" integer,
  "state_id" integer,
  "city_id" integer,
  "is_active" tinyint(1) not null default '1',
  "is_verified" tinyint(1) not null default '1',
  "owner_id" integer,
  "owner_type" varchar,
  "language" varchar not null default 'en',
  "profile_views" integer not null default '0',
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "facebook_url" varchar,
  "twitter_url" varchar,
  "linkedin_url" varchar,
  "google_plus_url" varchar,
  "pinterest_url" varchar,
  "is_default" tinyint(1) not null default '0',
  "stripe_id" varchar,
  "region_code" varchar,
  "theme_mode" varchar default '0',
  "profile_image_path" varchar,
  "user_type" varchar check("user_type" in('candidate', 'employer', 'admin')) not null default 'candidate',
  "name" varchar,
  "deleted_at" datetime,
  foreign key("country_id") references "countries"("id") on delete set null on update cascade,
  foreign key("state_id") references "states"("id") on delete set null on update cascade,
  foreign key("city_id") references "cities"("id") on delete set null on update cascade
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime
);
CREATE INDEX "password_resets_email_index" on "password_reset_tokens"("email");
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP,
  "uuid" varchar
);
CREATE TABLE IF NOT EXISTS "personal_access_tokens"(
  "id" integer primary key autoincrement not null,
  "tokenable_type" varchar not null,
  "tokenable_id" integer not null,
  "name" varchar not null,
  "token" varchar not null,
  "abilities" text,
  "last_used_at" datetime,
  "expires_at" datetime,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "personal_access_tokens_tokenable_type_tokenable_id_index" on "personal_access_tokens"(
  "tokenable_type",
  "tokenable_id"
);
CREATE UNIQUE INDEX "personal_access_tokens_token_unique" on "personal_access_tokens"(
  "token"
);
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "job_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "is_featured" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "image_path" varchar,
  "is_active" tinyint(1) not null default '1'
);
CREATE UNIQUE INDEX "job_categories_name_unique" on "job_categories"("name");
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "company_sizes"(
  "id" integer primary key autoincrement not null,
  "size" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1'
);
CREATE UNIQUE INDEX "company_sizes_size_unique" on "company_sizes"("size");
CREATE TABLE IF NOT EXISTS "industries"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "industries_name_unique" on "industries"("name");
CREATE TABLE IF NOT EXISTS "ownership_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "ownership_types_name_unique" on "ownership_types"("name");
CREATE TABLE IF NOT EXISTS "tags"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "job_tags_name_unique" on "tags"("name");
CREATE TABLE IF NOT EXISTS "job_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "slug" varchar,
  "icon" varchar,
  "color" varchar,
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "meta_title" varchar,
  "meta_description" text,
  "meta_keywords" text,
  "views_count" integer not null default '0',
  "jobs_count" integer not null default '0',
  "settings" text,
  "extra_attributes" text
);
CREATE UNIQUE INDEX "job_types_name_unique" on "job_types"("name");
CREATE TABLE IF NOT EXISTS "salary_periods"(
  "id" integer primary key autoincrement not null,
  "period" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "salary_periods_period_unique" on "salary_periods"(
  "period"
);
CREATE TABLE IF NOT EXISTS "job_shifts"(
  "id" integer primary key autoincrement not null,
  "shift" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "job_shifts_shift_unique" on "job_shifts"("shift");
CREATE TABLE IF NOT EXISTS "marital_status"(
  "id" integer primary key autoincrement not null,
  "marital_status" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "marital_status_marital_status_unique" on "marital_status"(
  "marital_status"
);
CREATE TABLE IF NOT EXISTS "required_degree_levels"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "required_degree_levels_name_unique" on "required_degree_levels"(
  "name"
);
CREATE TABLE IF NOT EXISTS "languages"(
  "id" integer primary key autoincrement not null,
  "language" varchar not null,
  "iso_code" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE TABLE IF NOT EXISTS "functional_areas"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0'
);
CREATE UNIQUE INDEX "functional_areas_name_unique" on "functional_areas"(
  "name"
);
CREATE TABLE IF NOT EXISTS "career_levels"(
  "id" integer primary key autoincrement not null,
  "level_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1'
);
CREATE UNIQUE INDEX "career_levels_level_name_unique" on "career_levels"(
  "level_name"
);
CREATE TABLE IF NOT EXISTS "salary_currencies"(
  "id" integer primary key autoincrement not null,
  "currency_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "currency_icon" varchar not null default '$',
  "currency_code" varchar not null
);
CREATE UNIQUE INDEX "salary_currencies_currency_name_unique" on "salary_currencies"(
  "currency_name"
);
CREATE TABLE IF NOT EXISTS "skills"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "jobs_skill"(
  "id" integer primary key autoincrement not null,
  "job_id" integer not null,
  "skill_id" integer not null,
  foreign key("job_id") references "jobs"("id") on delete CASCADE on update CASCADE,
  foreign key("skill_id") references "skills"("id") on delete CASCADE on update CASCADE
);
CREATE TABLE IF NOT EXISTS "testimonials"(
  "id" integer primary key autoincrement not null,
  "customer_name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "image_path" varchar,
  "customer_title" varchar,
  "customer_company" varchar,
  "customer_email" varchar,
  "rating" integer not null default '5',
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "is_verified" tinyint(1) not null default '0',
  "location" varchar,
  "project_type" varchar,
  "sort_order" integer,
  "testimonial_date" date,
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "favourite_jobs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "job_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("job_id") references "jobs"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "reported_jobs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "job_id" integer not null,
  "note" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("job_id") references "jobs"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "candidate_experiences"(
  "id" integer primary key autoincrement not null,
  "candidate_id" integer not null,
  "experience_title" varchar not null,
  "company" varchar not null,
  "country_id" integer,
  "state_id" integer,
  "city_id" integer,
  "start_date" date not null,
  "end_date" date,
  "currently_working" tinyint(1) not null default '0',
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "job_level" varchar,
  "employment_type" varchar,
  "salary" numeric,
  "is_verified" tinyint(1) not null default '0',
  foreign key("candidate_id") references "candidates"("id") on delete cascade on update cascade,
  foreign key("country_id") references "countries"("id") on delete set null on update cascade,
  foreign key("state_id") references "states"("id") on delete set null on update cascade,
  foreign key("city_id") references "cities"("id") on delete set null on update cascade
);
CREATE TABLE IF NOT EXISTS "email_jobs"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "job_id" integer not null,
  "job_url" varchar not null,
  "friend_name" varchar not null,
  "friend_email" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("job_id") references "jobs"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "favourite_companies"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "company_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("company_id") references "companies"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "reported_to_companies"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "company_id" integer not null,
  "note" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("company_id") references "companies"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "candidate_educations"(
  "id" integer primary key autoincrement not null,
  "candidate_id" integer not null,
  "degree_level_id" integer not null,
  "degree_title" varchar not null,
  "country_id" integer,
  "state_id" integer,
  "city_id" integer,
  "institute" varchar not null,
  "result" varchar not null,
  "year" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "grade_percentage" numeric,
  "field_of_study" varchar,
  "description" text,
  "is_verified" tinyint(1) not null default '0',
  foreign key("candidate_id") references "candidates"("id") on delete cascade on update cascade,
  foreign key("degree_level_id") references "required_degree_levels"("id") on delete cascade on update cascade,
  foreign key("country_id") references "countries"("id") on delete set null on update cascade,
  foreign key("state_id") references "states"("id") on delete set null on update cascade,
  foreign key("city_id") references "cities"("id") on delete set null on update cascade
);
CREATE TABLE IF NOT EXISTS "candidate_language"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "language_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("language_id") references "languages"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "news_letters"(
  "id" integer primary key autoincrement not null,
  "email" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "news_letters_email_unique" on "news_letters"("email");
CREATE TABLE IF NOT EXISTS "noticeboards"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "candidate_skills"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "skill_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("skill_id") references "skills"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "faqs"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "inquiries"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone_no" varchar,
  "subject" varchar not null,
  "message" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_active" tinyint(1) not null default '1'
);
CREATE TABLE IF NOT EXISTS "post_categories"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer,
  "color" varchar,
  "icon" varchar,
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "posts"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "description" text not null,
  "created_by" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_default" tinyint(1) not null default '0',
  "image_path" varchar,
  "deleted_at" datetime,
  foreign key("created_by") references "users"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "post_assigned_categories"(
  "id" integer primary key autoincrement not null,
  "post_id" integer not null,
  "post_categories_id" integer not null,
  foreign key("post_id") references "posts"("id") on delete cascade on update cascade,
  foreign key("post_categories_id") references "post_categories"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "reported_to_candidates"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "candidate_id" integer not null,
  "note" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade,
  foreign key("candidate_id") references "candidates"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "jobs_tag"(
  "id" integer primary key autoincrement not null,
  "job_id" integer not null,
  "tag_id" integer not null,
  foreign key("job_id") references "jobs"("id") on delete CASCADE on update CASCADE,
  foreign key("tag_id") references "tags"("id") on delete CASCADE on update CASCADE
);
CREATE TABLE IF NOT EXISTS "plans"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "stripe_plan_id" varchar,
  "allowed_jobs" integer not null,
  "amount" double not null,
  "is_trial_plan" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "salary_currency_id" integer not null,
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "priority_support" tinyint(1) not null default '0',
  "analytics_access" tinyint(1) not null default '0',
  "max_featured_jobs" integer not null default '0',
  "duration_days" integer not null default '30'
);
CREATE UNIQUE INDEX "plans_name_unique" on "plans"("name");
CREATE TABLE IF NOT EXISTS "subscription_items"(
  "id" integer primary key autoincrement not null,
  "subscription_id" integer not null,
  "stripe_id" varchar not null,
  "stripe_plan" varchar not null,
  "quantity" integer not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "subscription_items_subscription_id_stripe_plan_unique" on "subscription_items"(
  "subscription_id",
  "stripe_plan"
);
CREATE INDEX "subscription_items_stripe_id_index" on "subscription_items"(
  "stripe_id"
);
CREATE TABLE IF NOT EXISTS "social_accounts"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "provider" varchar not null,
  "provider_id" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade
);
CREATE UNIQUE INDEX "social_accounts_user_id_unique" on "social_accounts"(
  "user_id"
);
CREATE UNIQUE INDEX "social_accounts_provider_unique" on "social_accounts"(
  "provider"
);
CREATE UNIQUE INDEX "social_accounts_provider_id_unique" on "social_accounts"(
  "provider_id"
);
CREATE TABLE IF NOT EXISTS "front_settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "header_logo_path" varchar,
  "footer_logo_path" varchar
);
CREATE TABLE IF NOT EXISTS "featured_records"(
  "id" integer primary key autoincrement not null,
  "owner_id" integer not null,
  "owner_type" varchar not null,
  "user_id" integer not null,
  "stripe_id" varchar,
  "start_time" datetime not null,
  "end_time" datetime not null,
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  "is_active" tinyint(1) not null default '1',
  foreign key("user_id") references users("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "jobs_alerts"(
  "id" integer primary key autoincrement not null,
  "candidate_id" integer not null,
  "job_type_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("candidate_id") references "candidates"("id") on delete cascade on update cascade,
  foreign key("job_type_id") references "job_types"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "image_sliders"(
  "id" integer primary key autoincrement not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "image_path" varchar
);
CREATE TABLE IF NOT EXISTS "notifications"(
  "id" integer primary key autoincrement not null,
  "type" integer not null,
  "notification_for" integer not null,
  "user_id" integer not null,
  "title" varchar not null,
  "text" text,
  "meta" text,
  "read_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "notification_settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "type" varchar
);
CREATE TABLE IF NOT EXISTS "header_sliders"(
  "id" integer primary key autoincrement not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "image_path" varchar,
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "subscriptions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "name" varchar not null,
  "stripe_id" varchar,
  "stripe_status" varchar,
  "stripe_plan" varchar,
  "plan_id" integer,
  "trial_ends_at" datetime,
  "ends_at" datetime,
  "current_period_start" datetime,
  "current_period_end" datetime,
  "cancellation_reason" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "type" varchar not null default '1',
  "paypal_payment_id" varchar,
  "stripe_price" varchar,
  foreign key("plan_id") references plans("id") on delete cascade on update cascade,
  foreign key("user_id") references users("id") on delete cascade on update cascade
);
CREATE INDEX "subscriptions_user_id_stripe_status_index" on "subscriptions"(
  "user_id",
  "stripe_status"
);
CREATE TABLE IF NOT EXISTS "branding_sliders"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  "image_path" varchar,
  "view_count" integer not null default '0',
  "click_count" integer not null default '0',
  "sort_order" integer not null default '0',
  "open_in_new_tab" tinyint(1) not null default '0',
  "deleted_at" datetime,
  "description" text,
  "link_url" varchar,
  "button_text" varchar,
  "is_featured" tinyint(1) not null default '0',
  "start_date" datetime,
  "end_date" datetime,
  "meta" text
);
CREATE TABLE IF NOT EXISTS "email_templates"(
  "id" integer primary key autoincrement not null,
  "template_name" varchar not null,
  "subject" varchar not null,
  "body" text not null,
  "variables" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "post_comments"(
  "id" integer primary key autoincrement not null,
  "name" varchar,
  "email" varchar not null,
  "comment" text not null,
  "post_id" integer not null,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("post_id") references "posts"("id") on delete cascade on update cascade,
  foreign key("user_id") references "users"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "job_stages"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "company_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("company_id") references "companies"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "job_applications"(
  "id" integer primary key autoincrement not null,
  "job_id" integer not null,
  "candidate_id" integer not null,
  "resume_id" integer not null,
  "expected_salary" double not null,
  "notes" text,
  "status" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "job_stage_id" integer,
  foreign key("candidate_id") references candidates("id") on delete cascade on update cascade,
  foreign key("job_id") references jobs("id") on delete cascade on update cascade,
  foreign key("job_stage_id") references "job_stages"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "job_application_schedules"(
  "id" integer primary key autoincrement not null,
  "job_application_id" integer not null,
  "stage_id" integer not null,
  "time" varchar not null,
  "date" varchar not null,
  "notes" text,
  "status" integer,
  "batch" integer,
  "rejected_slot_notes" text,
  "employer_cancel_slot_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("job_application_id") references "job_applications"("id") on delete cascade on update cascade,
  foreign key("stage_id") references "job_stages"("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "cms_services"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  "image_path" varchar,
  "deleted_at" datetime
);
CREATE TABLE IF NOT EXISTS "transactions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "subscription_id" integer not null,
  "invoice_id" varchar,
  "amount" float,
  "created_at" datetime,
  "updated_at" datetime,
  "status" integer not null default('1'),
  "is_approved" integer not null default('1'),
  "approved_id" integer,
  "plan_currency_id" integer,
  foreign key("subscription_id") references subscriptions("id") on delete cascade on update cascade,
  foreign key("user_id") references users("id") on delete cascade on update cascade,
  foreign key("approved_id") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "job_id" varchar not null,
  "job_title" varchar not null,
  "description" text,
  "country_id" integer,
  "state_id" integer,
  "city_id" integer,
  "salary_from" double not null,
  "salary_to" double not null,
  "company_id" integer not null,
  "job_category_id" integer not null,
  "currency_id" integer not null,
  "salary_period_id" integer not null,
  "job_type_id" integer not null,
  "career_level_id" integer,
  "functional_area_id" integer not null,
  "job_shift_id" integer,
  "degree_level_id" integer,
  "position" integer not null,
  "job_expiry_date" date not null,
  "no_preference" integer,
  "hide_salary" tinyint(1) not null,
  "is_freelance" tinyint(1) not null,
  "status" integer not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "is_suspended" tinyint(1) not null default('0'),
  "experience" integer,
  "is_default" tinyint(1) not null default('0'),
  "is_created_by_admin" integer not null default('0'),
  "last_change" integer,
  "key_responsibilities" text,
  "deleted_at" datetime,
  "is_featured" tinyint(1) not null default '0',
  foreign key("city_id") references cities("id") on delete set null on update cascade,
  foreign key("state_id") references states("id") on delete set null on update cascade,
  foreign key("country_id") references countries("id") on delete set null on update cascade,
  foreign key("job_category_id") references job_categories("id") on delete cascade on update cascade,
  foreign key("degree_level_id") references required_degree_levels("id") on delete cascade on update cascade,
  foreign key("job_shift_id") references job_shifts("id") on delete cascade on update cascade,
  foreign key("functional_area_id") references functional_areas("id") on delete cascade on update cascade,
  foreign key("career_level_id") references career_levels("id") on delete cascade on update cascade,
  foreign key("job_type_id") references job_types("id") on delete cascade on update cascade,
  foreign key("currency_id") references salary_currencies("id") on delete cascade on update cascade,
  foreign key("salary_period_id") references salary_periods("id") on delete cascade on update cascade,
  foreign key("company_id") references companies("id") on delete cascade on update cascade,
  foreign key("last_change") references "users"("id")
);
CREATE TABLE IF NOT EXISTS "env_settings"(
  "id" integer primary key autoincrement not null,
  "key" varchar not null,
  "value" text not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "files"(
  "id" integer primary key autoincrement not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  "collection_name" varchar not null,
  "name" varchar not null,
  "file_name" varchar not null,
  "mime_type" varchar,
  "disk" varchar not null,
  "path" varchar not null,
  "size" integer not null,
  "order_column" integer,
  "custom_properties" text,
  "responsive_images" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "files_model_type_model_id_index" on "files"(
  "model_type",
  "model_id"
);
CREATE TABLE IF NOT EXISTS "todos"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "title" varchar not null,
  "description" text,
  "due_date" datetime,
  "is_completed" tinyint(1) not null default '0',
  "priority" varchar not null default 'medium',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "todos_user_id_index" on "todos"("user_id");
CREATE INDEX "todos_is_completed_index" on "todos"("is_completed");
CREATE INDEX "todos_priority_index" on "todos"("priority");
CREATE TABLE IF NOT EXISTS "queue_jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "queue_jobs_queue_index" on "queue_jobs"("queue");
CREATE INDEX "jobs_status_index" on "jobs"("status");
CREATE INDEX "jobs_created_at_index" on "jobs"("created_at");
CREATE INDEX "jobs_company_id_index" on "jobs"("company_id");
CREATE INDEX "jobs_status_created_at_index" on "jobs"("status", "created_at");
CREATE INDEX "users_user_type_index" on "users"("user_type");
CREATE INDEX "users_created_at_index" on "users"("created_at");
CREATE TABLE IF NOT EXISTS "activity_log"(
  "id" integer primary key autoincrement not null,
  "log_name" varchar,
  "description" text not null,
  "subject_type" varchar,
  "subject_id" integer,
  "causer_type" varchar,
  "causer_id" integer,
  "properties" text,
  "created_at" datetime,
  "updated_at" datetime,
  "event" varchar,
  "batch_uuid" varchar
);
CREATE INDEX "subject" on "activity_log"("subject_type", "subject_id");
CREATE INDEX "causer" on "activity_log"("causer_type", "causer_id");
CREATE INDEX "activity_log_log_name_index" on "activity_log"("log_name");
CREATE TABLE IF NOT EXISTS "media"(
  "id" integer primary key autoincrement not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  "uuid" varchar,
  "collection_name" varchar not null,
  "name" varchar not null,
  "file_name" varchar not null,
  "mime_type" varchar,
  "disk" varchar not null,
  "conversions_disk" varchar,
  "size" integer not null,
  "manipulations" text not null,
  "custom_properties" text not null,
  "generated_conversions" text not null,
  "responsive_images" text not null,
  "order_column" integer,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "media_model_type_model_id_index" on "media"(
  "model_type",
  "model_id"
);
CREATE UNIQUE INDEX "media_uuid_unique" on "media"("uuid");
CREATE UNIQUE INDEX "job_types_slug_unique" on "job_types"("slug");
CREATE INDEX "job_types_active_featured_index" on "job_types"(
  "is_active",
  "is_featured"
);
CREATE INDEX "job_types_sort_name_index" on "job_types"("sort_order", "name");
CREATE INDEX "job_types_default_active_index" on "job_types"(
  "is_default",
  "is_active"
);
CREATE INDEX "job_types_jobs_count_index" on "job_types"("jobs_count");
CREATE TABLE IF NOT EXISTS "taxonomies"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "type" varchar not null default 'category',
  "is_hierarchical" tinyint(1) not null default '1',
  "is_active" tinyint(1) not null default '1',
  "is_public" tinyint(1) not null default '1',
  "meta" text,
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE INDEX "taxonomies_type_is_active_index" on "taxonomies"(
  "type",
  "is_active"
);
CREATE INDEX "taxonomies_slug_index" on "taxonomies"("slug");
CREATE UNIQUE INDEX "taxonomies_slug_unique" on "taxonomies"("slug");
CREATE TABLE IF NOT EXISTS "terms"(
  "id" integer primary key autoincrement not null,
  "taxonomy_id" integer not null,
  "name" varchar not null,
  "slug" varchar not null,
  "description" text,
  "color" varchar,
  "icon" varchar,
  "image" varchar,
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "sort_order" integer not null default '0',
  "meta" text,
  "parent_id" integer,
  "level" integer not null default '0',
  "path" varchar,
  "usage_count" integer not null default '0',
  "last_used_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("taxonomy_id") references "taxonomies"("id") on delete cascade,
  foreign key("parent_id") references "terms"("id") on delete cascade
);
CREATE UNIQUE INDEX "terms_taxonomy_id_slug_unique" on "terms"(
  "taxonomy_id",
  "slug"
);
CREATE INDEX "terms_taxonomy_id_is_active_index" on "terms"(
  "taxonomy_id",
  "is_active"
);
CREATE INDEX "terms_parent_id_sort_order_index" on "terms"(
  "parent_id",
  "sort_order"
);
CREATE INDEX "terms_usage_count_index" on "terms"("usage_count");
CREATE TABLE IF NOT EXISTS "taggables"(
  "id" integer primary key autoincrement not null,
  "term_id" integer not null,
  "taggable_type" varchar not null,
  "taggable_id" integer not null,
  "taxonomy_id" integer not null,
  "sort_order" integer not null default '0',
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("term_id") references "terms"("id") on delete cascade,
  foreign key("taxonomy_id") references "taxonomies"("id") on delete cascade
);
CREATE INDEX "taggables_taggable_type_taggable_id_index" on "taggables"(
  "taggable_type",
  "taggable_id"
);
CREATE UNIQUE INDEX "taggables_term_id_taggable_id_taggable_type_unique" on "taggables"(
  "term_id",
  "taggable_id",
  "taggable_type"
);
CREATE INDEX "taggables_taxonomy_id_term_id_index" on "taggables"(
  "taxonomy_id",
  "term_id"
);
CREATE INDEX "countries_is_active_index" on "countries"("is_active");
CREATE INDEX "countries_is_featured_index" on "countries"("is_featured");
CREATE INDEX "countries_is_default_index" on "countries"("is_default");
CREATE INDEX "countries_region_index" on "countries"("region");
CREATE INDEX "countries_continent_index" on "countries"("continent");
CREATE INDEX "countries_is_active_is_featured_index" on "countries"(
  "is_active",
  "is_featured"
);
CREATE INDEX "candidate_educations_is_verified_index" on "candidate_educations"(
  "is_verified"
);
CREATE INDEX "candidate_educations_grade_percentage_index" on "candidate_educations"(
  "grade_percentage"
);
CREATE INDEX "candidate_educations_field_of_study_index" on "candidate_educations"(
  "field_of_study"
);
CREATE INDEX "candidate_educations_year_index" on "candidate_educations"(
  "year"
);
CREATE INDEX "candidate_experiences_job_level_index" on "candidate_experiences"(
  "job_level"
);
CREATE INDEX "candidate_experiences_employment_type_index" on "candidate_experiences"(
  "employment_type"
);
CREATE INDEX "candidate_experiences_is_verified_index" on "candidate_experiences"(
  "is_verified"
);
CREATE INDEX "candidate_experiences_salary_index" on "candidate_experiences"(
  "salary"
);
CREATE TABLE IF NOT EXISTS "cities"(
  "id" integer primary key autoincrement not null,
  "state_id" integer not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "is_active" tinyint(1) not null default '1',
  "is_featured" tinyint(1) not null default '0',
  "is_metropolitan" tinyint(1) not null default '0',
  "is_major" tinyint(1) not null default '0',
  "latitude" numeric,
  "longitude" numeric,
  "timezone" varchar,
  "population" integer,
  "deleted_at" datetime,
  foreign key("state_id") references states("id") on delete cascade on update cascade
);
CREATE TABLE IF NOT EXISTS "candidates"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "unique_id" varchar not null,
  "father_name" varchar,
  "marital_status_id" integer,
  "nationality" varchar,
  "national_id_card" varchar,
  "experience" integer,
  "career_level_id" integer,
  "industry_id" integer,
  "functional_area_id" integer,
  "current_salary" double,
  "expected_salary" double,
  "salary_currency" varchar,
  "address" text,
  "immediate_available" tinyint(1) not null default('1'),
  "created_at" datetime,
  "updated_at" datetime,
  "job_alert" tinyint(1) not null default('0'),
  "available_at" date,
  "last_change" integer,
  "resume_path" varchar,
  "image_path" varchar,
  "city_id" integer,
  foreign key("last_change") references users("id") on delete no action on update no action,
  foreign key("user_id") references users("id") on delete cascade on update cascade,
  foreign key("marital_status_id") references marital_status("id") on delete cascade on update cascade,
  foreign key("career_level_id") references career_levels("id") on delete cascade on update cascade,
  foreign key("functional_area_id") references functional_areas("id") on delete cascade on update cascade,
  foreign key("industry_id") references industries("id") on delete cascade on update cascade,
  foreign key("city_id") references "cities"("id") on delete set null on update cascade
);
CREATE INDEX "plans_is_active_index" on "plans"("is_active");
CREATE INDEX "plans_is_featured_index" on "plans"("is_featured");
CREATE INDEX "plans_is_active_is_featured_index" on "plans"(
  "is_active",
  "is_featured"
);
CREATE TABLE IF NOT EXISTS "companies"(
  "id" integer primary key autoincrement not null,
  "ceo" varchar,
  "no_of_offices" integer,
  "user_id" integer,
  "industry_id" integer,
  "ownership_type_id" integer,
  "company_size_id" integer,
  "established_in" integer,
  "details" text,
  "website" varchar,
  "location" varchar,
  "is_featured" tinyint(1) not null default('0'),
  "fax" varchar,
  "unique_id" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "location2" varchar,
  "last_change" integer,
  "logo_path" varchar,
  "facebook_url" varchar,
  "twitter_url" varchar,
  "linkedin_url" varchar,
  "google_plus_url" varchar,
  "pinterest_url" varchar,
  "slug" varchar,
  "deleted_at" datetime,
  "name" varchar,
  "email" varchar,
  "phone" varchar,
  "city_id" integer,
  "is_active" tinyint(1) not null default('1'),
  "country_id" integer,
  "state_id" integer,
  "is_verified" tinyint(1) not null default '0',
  "no_of_employees" integer,
  foreign key("city_id") references cities("id") on delete set null on update cascade,
  foreign key("user_id") references users("id") on delete cascade on update cascade,
  foreign key("industry_id") references industries("id") on delete cascade on update cascade,
  foreign key("ownership_type_id") references ownership_types("id") on delete cascade on update cascade,
  foreign key("company_size_id") references company_sizes("id") on delete cascade on update cascade,
  foreign key("last_change") references users("id") on delete no action on update no action,
  foreign key("country_id") references "countries"("id") on delete set null on update cascade,
  foreign key("state_id") references "states"("id") on delete set null on update cascade
);
CREATE INDEX "companies_created_at_index" on "companies"("created_at");
CREATE INDEX "companies_is_featured_index" on "companies"("is_featured");
CREATE UNIQUE INDEX "companies_slug_unique" on "companies"("slug");
CREATE UNIQUE INDEX "companies_unique_id_unique" on "companies"("unique_id");
CREATE INDEX "featured_records_owner_index" on "featured_records"(
  "owner_id",
  "owner_type"
);
CREATE INDEX "featured_records_time_index" on "featured_records"(
  "start_time",
  "end_time"
);
CREATE INDEX "featured_records_is_active_index" on "featured_records"(
  "is_active"
);

INSERT INTO migrations VALUES(1,'2014_10_10_045631_create_countries_table',1);
INSERT INTO migrations VALUES(2,'2014_10_12_045650_create_states_table',1);
INSERT INTO migrations VALUES(3,'2014_10_12_045711_create_cities_table',1);
INSERT INTO migrations VALUES(4,'2014_10_12_045722_create_users_table',1);
INSERT INTO migrations VALUES(5,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO migrations VALUES(6,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO migrations VALUES(7,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO migrations VALUES(8,'2020_06_19_071110_create_media_table',1);
INSERT INTO migrations VALUES(9,'2020_06_19_071420_create_permission_tables',1);
INSERT INTO migrations VALUES(10,'2020_06_19_102134_create_job_categories_table',1);
INSERT INTO migrations VALUES(11,'2020_06_20_082711_create_settings_table',1);
INSERT INTO migrations VALUES(12,'2020_06_20_121439_create_company_sizes_table',1);
INSERT INTO migrations VALUES(13,'2020_06_20_134118_create_industries_table',1);
INSERT INTO migrations VALUES(14,'2020_06_22_094719_create_owner_ship_types_table',1);
INSERT INTO migrations VALUES(15,'2020_06_22_115654_create_job_tags_table',1);
INSERT INTO migrations VALUES(16,'2020_06_22_123442_create_companies_table',1);
INSERT INTO migrations VALUES(17,'2020_06_22_124347_create_job_types_table',1);
INSERT INTO migrations VALUES(18,'2020_06_23_095255_create_salary_periods_table',1);
INSERT INTO migrations VALUES(19,'2020_06_23_105948_create_job_shifts_table',1);
INSERT INTO migrations VALUES(20,'2020_06_23_125514_create_marital_status_table',1);
INSERT INTO migrations VALUES(21,'2020_06_30_123008_create_required_degree_levels_table',1);
INSERT INTO migrations VALUES(22,'2020_07_03_091217_create_languages_table',1);
INSERT INTO migrations VALUES(23,'2020_07_04_072608_create_functional_areas_table',1);
INSERT INTO migrations VALUES(24,'2020_07_07_050739_create_career_levels_table',1);
INSERT INTO migrations VALUES(25,'2020_07_07_064147_create_salary_currencies_table',1);
INSERT INTO migrations VALUES(26,'2020_07_10_052359_create_skills_table',1);
INSERT INTO migrations VALUES(27,'2020_07_11_102026_create_jobs_table',1);
INSERT INTO migrations VALUES(28,'2020_07_11_130415_create_jobs_skill_table',1);
INSERT INTO migrations VALUES(29,'2020_07_13_113119_add_suspended_and_is_featured_column_into_jobs_table',1);
INSERT INTO migrations VALUES(30,'2020_07_20_054803_create_candidates_table',1);
INSERT INTO migrations VALUES(31,'2020_07_22_093729_create_testimonials_table',1);
INSERT INTO migrations VALUES(32,'2020_07_22_094459_create_favourite_jobs_table',1);
INSERT INTO migrations VALUES(33,'2020_07_22_122321_create_reported_jobs_table',1);
INSERT INTO migrations VALUES(34,'2020_07_22_133224_create_job_applications_table',1);
INSERT INTO migrations VALUES(35,'2020_07_23_111237_create_candidate_experiences_table',1);
INSERT INTO migrations VALUES(36,'2020_07_23_111414_create_email_jobs_table',1);
INSERT INTO migrations VALUES(37,'2020_07_24_072123_create_favourite_companies_table',1);
INSERT INTO migrations VALUES(38,'2020_07_24_094449_create_reported_to_companies_table',1);
INSERT INTO migrations VALUES(39,'2020_07_24_112818_create_candidate_educations_table',1);
INSERT INTO migrations VALUES(40,'2020_07_29_115041_create_candidate_language_table',1);
INSERT INTO migrations VALUES(41,'2020_07_30_053934_create_news_letters_table',1);
INSERT INTO migrations VALUES(42,'2020_07_30_081848_create_noticeboards_table',1);
INSERT INTO migrations VALUES(43,'2020_07_30_084222_create_candidate_skills_table',1);
INSERT INTO migrations VALUES(44,'2020_07_30_093609_create_faqs_table',1);
INSERT INTO migrations VALUES(45,'2020_07_31_040917_create_inquiries_table',1);
INSERT INTO migrations VALUES(46,'2020_07_31_050755_create_post_categories_table',1);
INSERT INTO migrations VALUES(47,'2020_07_31_061741_create_posts_table',1);
INSERT INTO migrations VALUES(48,'2020_07_31_064138_create_post_assigned_categories_table',1);
INSERT INTO migrations VALUES(49,'2020_08_14_115324_remove_social_links_from_companies',1);
INSERT INTO migrations VALUES(50,'2020_08_14_120001_add_social_links_to_users',1);
INSERT INTO migrations VALUES(51,'2020_08_20_050324_create_reported_to_candidates_table',1);
INSERT INTO migrations VALUES(52,'2020_08_20_061202_changes_on_columns_to_jobs',1);
INSERT INTO migrations VALUES(53,'2020_08_22_044709_rename_job_tags_to_tags',1);
INSERT INTO migrations VALUES(54,'2020_08_22_045051_create_jobs_tag_table',1);
INSERT INTO migrations VALUES(55,'2020_08_22_055108_add_experience_column_to_jobs',1);
INSERT INTO migrations VALUES(56,'2020_08_22_091337_add_default_flag_into_tables',1);
INSERT INTO migrations VALUES(57,'2020_09_28_121913_create_plans_table',1);
INSERT INTO migrations VALUES(58,'2020_09_28_121914_create_subscriptions_table',1);
INSERT INTO migrations VALUES(59,'2020_09_28_122226_create_transactions_table',1);
INSERT INTO migrations VALUES(60,'2020_09_29_122228_create_subscription_items_table',1);
INSERT INTO migrations VALUES(61,'2020_09_30_123653_add_stripe_id_in_users_table',1);
INSERT INTO migrations VALUES(62,'2020_10_01_101638_create_featured_records_table',1);
INSERT INTO migrations VALUES(63,'2020_10_01_103316_create_social_accounts_table',1);
INSERT INTO migrations VALUES(64,'2020_10_01_105745_create_front_settings_table',1);
INSERT INTO migrations VALUES(65,'2020_10_05_130353_add_soft_deletes_to_plans_table',1);
INSERT INTO migrations VALUES(66,'2020_10_06_073926_changes_on_columns_in_transactions_table',1);
INSERT INTO migrations VALUES(67,'2020_10_10_053314_drop_featured_column_from_companies_and_jobs',1);
INSERT INTO migrations VALUES(68,'2020_10_12_050703_nullable_stripe_id_to_featured_records_table',1);
INSERT INTO migrations VALUES(69,'2020_10_16_122711_add_job_alert_field_to_candidates_table',1);
INSERT INTO migrations VALUES(70,'2020_10_16_123513_create_jobs_alerts_table',1);
INSERT INTO migrations VALUES(71,'2020_10_27_121154_add_region_code_to_users',1);
INSERT INTO migrations VALUES(72,'2020_11_06_111241_create_image_slider_table',1);
INSERT INTO migrations VALUES(73,'2020_11_23_100925_create_notifications_table',1);
INSERT INTO migrations VALUES(74,'2020_11_24_113854_add_icon_in_salary_currencies_table',1);
INSERT INTO migrations VALUES(75,'2020_11_28_091936_create_notification_settings_table',1);
INSERT INTO migrations VALUES(76,'2020_12_11_052318_add_type_in_notification_settings_table',1);
INSERT INTO migrations VALUES(77,'2020_12_16_042032_create_header_sliders_table',1);
INSERT INTO migrations VALUES(78,'2020_12_18_133145_add_paypal_payment_id_into_subscriptions_table',1);
INSERT INTO migrations VALUES(79,'2020_12_19_042028_create_branding_sliders_table',1);
INSERT INTO migrations VALUES(80,'2020_12_26_044333_add_available_at_in_candidates_table',1);
INSERT INTO migrations VALUES(81,'2021_02_09_091223_remove_provider_unique_rule_from_social_accounts',1);
INSERT INTO migrations VALUES(82,'2021_04_12_103529_add_salary_currency_id_into_plans_table',1);
INSERT INTO migrations VALUES(83,'2021_04_13_070142_add_currecy_code_to_salary_currencies_table',1);
INSERT INTO migrations VALUES(84,'2021_06_04_051824_create_email_templates_table',1);
INSERT INTO migrations VALUES(85,'2021_06_29_000000_add_uuid_to_failed_jobs_table',1);
INSERT INTO migrations VALUES(86,'2021_07_08_085344_create_post_comments_table',1);
INSERT INTO migrations VALUES(87,'2021_07_08_121050_add_column_is_created_by_admin_in_jobs_table',1);
INSERT INTO migrations VALUES(88,'2021_07_10_070048_create_job_stages_table',1);
INSERT INTO migrations VALUES(89,'2021_07_10_104206_add_job_stage_in_job_applications',1);
INSERT INTO migrations VALUES(90,'2021_07_10_114138_create_job_application_schedules_table',1);
INSERT INTO migrations VALUES(91,'2021_07_1_103036_add_conversions_disk_column_in_media_table',1);
INSERT INTO migrations VALUES(92,'2021_08_13_060723_create_location2_in_companies_table',1);
INSERT INTO migrations VALUES(93,'2021_11_23_101602_create_cms_services_table',1);
INSERT INTO migrations VALUES(94,'2022_03_02_104056_add_theme_mode_to_users_table',1);
INSERT INTO migrations VALUES(95,'2022_08_27_041123_add_payment_status_field_to_transactions_table',1);
INSERT INTO migrations VALUES(96,'2022_08_29_090208_add_is_approved_to_transactions_table',1);
INSERT INTO migrations VALUES(97,'2022_09_23_053617_add_approved_id_to_transactions_table',1);
INSERT INTO migrations VALUES(98,'2022_09_23_063254_add_last_change_to_jobs_table',1);
INSERT INTO migrations VALUES(99,'2022_09_23_072320_add_last_change_to_companies_table',1);
INSERT INTO migrations VALUES(100,'2022_09_23_075427_add_last_change_to_candidates_table',1);
INSERT INTO migrations VALUES(101,'2022_10_07_050613_add_plan_currency_id_to_transactions_table',1);
INSERT INTO migrations VALUES(102,'2022_12_30_070825_add_remove_sofrtdeletes_plans_table',1);
INSERT INTO migrations VALUES(103,'2023_06_15_000001_add_file_paths_to_models',1);
INSERT INTO migrations VALUES(104,'2023_06_15_000002_populate_logo_paths_from_media',1);
INSERT INTO migrations VALUES(105,'2023_06_15_000003_drop_media_tables',1);
INSERT INTO migrations VALUES(106,'2023_08_03_000000_rename_password_resets_table',1);
INSERT INTO migrations VALUES(107,'2023_08_28_120651_create_env_settings_table',1);
INSERT INTO migrations VALUES(108,'2023_09_02_073953_run_default_env_setting_seeder_table',1);
INSERT INTO migrations VALUES(109,'2023_12_01_105026_add_key_responsibilities_to_jobs_table',1);
INSERT INTO migrations VALUES(110,'2023_12_11_104535_add_paystack_key_to_env_settings_table',1);
INSERT INTO migrations VALUES(111,'2024_02_17_041054_run_reset_password_email_template',1);
INSERT INTO migrations VALUES(112,'2024_03_10_000000_create_files_table',1);
INSERT INTO migrations VALUES(113,'2024_03_22_050110_add_default_language_seeder',1);
INSERT INTO migrations VALUES(114,'2024_03_28_000001_add_image_and_resume_paths_to_candidates_table',1);
INSERT INTO migrations VALUES(115,'2024_03_28_000001_create_todos_table',1);
INSERT INTO migrations VALUES(116,'2024_03_28_000002_populate_candidate_image_and_resume_paths',1);
INSERT INTO migrations VALUES(117,'2024_04_01_071535_create_queue_jobs_table',1);
INSERT INTO migrations VALUES(118,'2025_06_03_221607_add_deleted_at_to_jobs_table',1);
INSERT INTO migrations VALUES(119,'2025_06_04_064619_add_user_type_to_users_table',1);
INSERT INTO migrations VALUES(120,'2025_06_04_091424_create_test_location_tables',1);
INSERT INTO migrations VALUES(121,'2025_06_04_103222_optimize_database_performance',1);
INSERT INTO migrations VALUES(122,'2025_06_05_162715_add_is_active_to_company_sizes_table',1);
INSERT INTO migrations VALUES(123,'2025_06_05_163544_add_social_media_columns_to_companies_table',1);
INSERT INTO migrations VALUES(124,'2025_06_05_163641_add_is_featured_to_jobs_table',1);
INSERT INTO migrations VALUES(125,'2025_06_05_163735_create_activity_log_table',1);
INSERT INTO migrations VALUES(126,'2025_06_05_163736_add_event_column_to_activity_log_table',1);
INSERT INTO migrations VALUES(127,'2025_06_05_163737_add_batch_uuid_column_to_activity_log_table',1);
INSERT INTO migrations VALUES(128,'2025_06_06_001101_add_missing_user_columns',1);
INSERT INTO migrations VALUES(129,'2025_06_06_001209_add_missing_subscription_columns',1);
INSERT INTO migrations VALUES(130,'2025_06_06_014611_recreate_media_table',1);
INSERT INTO migrations VALUES(131,'2025_06_08_104011_create_permissions_table',1);
INSERT INTO migrations VALUES(132,'2025_06_08_104012_create_roles_table',1);
INSERT INTO migrations VALUES(133,'2025_06_08_145303_add_is_active_to_job_categories_table',1);
INSERT INTO migrations VALUES(134,'2025_06_13_015244_enhance_job_types_table_fixed',1);
INSERT INTO migrations VALUES(135,'2025_06_13_043811_add_context7_fields_to_post_categories_table',1);
INSERT INTO migrations VALUES(136,'2025_06_14_164617_add_slug_to_companies_table',1);
INSERT INTO migrations VALUES(137,'2025_06_14_170028_create_taxonomies_table',1);
INSERT INTO migrations VALUES(138,'2025_06_14_170038_create_terms_table',1);
INSERT INTO migrations VALUES(139,'2025_06_14_170046_create_taggables_table',1);
INSERT INTO migrations VALUES(140,'2025_06_14_204949_add_deleted_at_to_skills_table',1);
INSERT INTO migrations VALUES(141,'2025_06_14_220452_add_missing_columns_to_branding_sliders_table',1);
INSERT INTO migrations VALUES(142,'2025_06_14_222242_add_deleted_at_to_branding_sliders_table',1);
INSERT INTO migrations VALUES(143,'2025_06_14_222258_add_deleted_at_to_branding_sliders_table',1);
INSERT INTO migrations VALUES(144,'2025_06_14_222431_add_missing_columns_to_branding_sliders',1);
INSERT INTO migrations VALUES(145,'2025_06_14_223208_add_deleted_at_to_users_table',1);
INSERT INTO migrations VALUES(146,'2025_06_14_231144_add_deleted_at_to_companies_table',1);
INSERT INTO migrations VALUES(147,'2025_06_14_231950_add_deleted_at_to_states_table',1);
INSERT INTO migrations VALUES(148,'2025_06_14_233208_add_is_active_to_career_levels_table',1);
INSERT INTO migrations VALUES(149,'2025_06_14_235920_add_missing_columns_to_testimonials_table',1);
INSERT INTO migrations VALUES(150,'2025_06_15_010505_add_missing_columns_to_countries_table',1);
INSERT INTO migrations VALUES(151,'2025_06_15_010647_add_missing_columns_to_candidate_educations_table',1);
INSERT INTO migrations VALUES(152,'2025_06_15_011847_add_missing_columns_to_candidate_experiences_table',1);
INSERT INTO migrations VALUES(153,'2025_06_15_012812_add_missing_name_email_to_companies',2);
INSERT INTO migrations VALUES(154,'2025_06_15_015458_add_missing_columns_to_states_table',3);
INSERT INTO migrations VALUES(155,'2025_06_15_015547_add_missing_columns_to_cms_services_table',4);
INSERT INTO migrations VALUES(157,'2025_06_15_015826_add_missing_columns_to_cities_table',5);
INSERT INTO migrations VALUES(158,'2025_06_15_021531_add_enhanced_columns_to_cities_table',6);
INSERT INTO migrations VALUES(160,'2025_06_15_023004_add_soft_deletes_to_cities_table',7);
INSERT INTO migrations VALUES(161,'2025_06_15_132453_add_city_id_to_companies_and_candidates_tables',7);
INSERT INTO migrations VALUES(162,'2025_06_15_132938_add_is_active_to_companies_table',8);
INSERT INTO migrations VALUES(163,'2025_06_15_133502_add_enhanced_columns_to_plans_table',9);
INSERT INTO migrations VALUES(164,'2025_06_15_142501_add_country_state_to_companies_table',10);
INSERT INTO migrations VALUES(165,'2025_06_15_153459_fix_featured_records_table_schema',11);
INSERT INTO migrations VALUES(166,'2025_06_15_153605_add_deleted_at_to_header_sliders_table',12);
INSERT INTO migrations VALUES(167,'2025_06_15_153711_add_deleted_at_to_posts_table',13);
INSERT INTO migrations VALUES(168,'2025_06_15_154118_add_missing_columns_to_companies_table_final',14);
INSERT INTO migrations VALUES(169,'2025_06_15_154318_add_is_active_to_inquiries_table',15);
