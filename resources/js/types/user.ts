// User Types and Interfaces for Laravel Job Portal
// Supports: Visitors, Candidates, Employers, Administrators

export type UserRole = 'visitor' | 'candidate' | 'employer' | 'admin';

export type UserStatus = 'active' | 'inactive' | 'pending' | 'suspended' | 'banned';

export type SubscriptionStatus = 'active' | 'inactive' | 'trial' | 'expired' | 'cancelled';

// Base User Interface
export interface BaseUser {
  id: number;
  email: string;
  email_verified_at: string | null;
  role: UserRole;
  status: UserStatus;
  created_at: string;
  updated_at: string;
  last_login_at: string | null;
  profile_image?: string;
  timezone?: string;
  locale?: string;
}

// Visitor Interface (Non-authenticated or limited access)
export interface Visitor {
  sessionId?: string;
  preferences?: {
    jobAlerts?: boolean;
    newsletterSubscription?: boolean;
    savedJobs?: number[];
    recentSearches?: string[];
  };
}

// Candidate Interface  
export interface Candidate extends BaseUser {
  role: 'candidate';
  profile: CandidateProfile;
  applications: JobApplication[];
  savedJobs: SavedJob[];
  jobAlerts: JobAlert[];
  resume?: Resume;
  portfolio?: Portfolio;
  preferences: CandidatePreferences;
}

export interface CandidateProfile {
  id: number;
  user_id: number;
  first_name: string;
  last_name: string;
  phone?: string;
  location?: Location;
  headline?: string;
  summary?: string;
  experience_level?: ExperienceLevel;
  availability?: Availability;
  salary_expectation?: SalaryRange;
  skills: Skill[];
  education: Education[];
  experience: WorkExperience[];
  languages: Language[];
  certifications: Certification[];
  social_links?: SocialLinks;
  profile_completion_percentage: number;
  is_public: boolean;
  is_available_for_hire: boolean;
}

// Employer Interface
export interface Employer extends BaseUser {
  role: 'employer';
  company: Company;
  postedJobs: Job[];
  subscription: Subscription;
  billingInfo: BillingInfo;
  teamMembers: TeamMember[];
  permissions: EmployerPermission[];
}

export interface Company {
  id: number;
  user_id: number;
  name: string;
  slug: string;
  description?: string;
  industry: Industry;
  company_size: CompanySize;
  founded_year?: number;
  website?: string;
  location: Location;
  logo?: string;
  cover_image?: string;
  social_links?: SocialLinks;
  benefits: string[];
  culture_values: string[];
  is_verified: boolean;
  verification_status: VerificationStatus;
  rating?: number;
  total_jobs_posted: number;
  total_applications_received: number;
}

// Administrator Interface
export interface Administrator extends BaseUser {
  role: 'admin';
  permissions: AdminPermission[];
  adminProfile: AdminProfile;
  lastAdminActivity: AdminActivity[];
}

export interface AdminProfile {
  id: number;
  user_id: number;
  first_name: string;
  last_name: string;
  department?: string;
  access_level: AdminAccessLevel;
  can_manage_users: boolean;
  can_manage_companies: boolean;
  can_manage_jobs: boolean;
  can_access_analytics: boolean;
  can_manage_system: boolean;
}

// Supporting Interfaces

export interface Location {
  id?: number;
  city: string;
  state: string;
  country: string;
  postal_code?: string;
  latitude?: number;
  longitude?: number;
  remote_allowed?: boolean;
}

export interface Skill {
  id: number;
  name: string;
  level: SkillLevel;
  years_of_experience?: number;
  is_primary?: boolean;
}

export interface Education {
  id: number;
  institution: string;
  degree: string;
  field_of_study: string;
  start_date: string;
  end_date?: string;
  grade?: string;
  description?: string;
  is_current: boolean;
}

export interface WorkExperience {
  id: number;
  company: string;
  position: string;
  location?: Location;
  start_date: string;
  end_date?: string;
  is_current: boolean;
  description?: string;
  skills_used: string[];
  achievements: string[];
}

export interface JobApplication {
  id: number;
  job_id: number;
  candidate_id: number;
  status: ApplicationStatus;
  applied_at: string;
  cover_letter?: string;
  resume_file?: string;
  custom_fields?: Record<string, any>;
  employer_notes?: string;
  interview_scheduled_at?: string;
  offer_details?: OfferDetails;
  rejection_reason?: string;
  timeline: ApplicationTimeline[];
}

export interface Job {
  id: number;
  company_id: number;
  title: string;
  slug: string;
  description: string;
  requirements: string[];
  responsibilities: string[];
  benefits: string[];
  employment_type: EmploymentType;
  experience_level: ExperienceLevel;
  salary_range?: SalaryRange;
  location: Location;
  remote_policy: RemotePolicy;
  skills_required: Skill[];
  status: JobStatus;
  posted_at: string;
  expires_at?: string;
  applications_count: number;
  views_count: number;
  is_featured: boolean;
  is_urgent: boolean;
  custom_fields?: Record<string, any>;
}

// Enums and Types

export type ExperienceLevel = 'entry' | 'junior' | 'mid' | 'senior' | 'lead' | 'executive';

export type EmploymentType = 'full-time' | 'part-time' | 'contract' | 'freelance' | 'internship' | 'temporary';

export type RemotePolicy = 'remote' | 'hybrid' | 'on-site' | 'flexible';

export type ApplicationStatus = 
  | 'draft' 
  | 'submitted' 
  | 'under_review' 
  | 'screening' 
  | 'interview_scheduled' 
  | 'interview_completed' 
  | 'reference_check' 
  | 'offer_extended' 
  | 'offer_accepted' 
  | 'offer_declined' 
  | 'hired' 
  | 'rejected' 
  | 'withdrawn';

export type JobStatus = 'draft' | 'published' | 'paused' | 'closed' | 'expired' | 'archived';

export type SkillLevel = 'beginner' | 'intermediate' | 'advanced' | 'expert';

export type CompanySize = '1-10' | '11-50' | '51-200' | '201-500' | '501-1000' | '1000+';

export type VerificationStatus = 'pending' | 'verified' | 'rejected' | 'under_review';

export type AdminAccessLevel = 'super_admin' | 'admin' | 'moderator' | 'support';

export type Availability = 'immediately' | 'within_week' | 'within_month' | 'within_3months' | 'not_looking';

// Additional Supporting Interfaces

export interface SalaryRange {
  min: number;
  max: number;
  currency: string;
  period: 'hourly' | 'daily' | 'weekly' | 'monthly' | 'yearly';
  is_negotiable: boolean;
}

export interface Industry {
  id: number;
  name: string;
  slug: string;
  description?: string;
  parent_id?: number;
}

export interface SocialLinks {
  linkedin?: string;
  github?: string;
  twitter?: string;
  portfolio?: string;
  website?: string;
  other?: Record<string, string>;
}

export interface Language {
  id: number;
  name: string;
  proficiency: 'basic' | 'conversational' | 'fluent' | 'native';
  is_primary?: boolean;
}

export interface Certification {
  id: number;
  name: string;
  issuing_organization: string;
  issue_date: string;
  expiry_date?: string;
  credential_id?: string;
  credential_url?: string;
  does_not_expire: boolean;
}

export interface Resume {
  id: number;
  candidate_id: number;
  filename: string;
  file_path: string;
  file_size: number;
  uploaded_at: string;
  is_primary: boolean;
  download_count: number;
}

export interface Portfolio {
  id: number;
  candidate_id: number;
  title: string;
  description?: string;
  projects: PortfolioProject[];
  is_public: boolean;
}

export interface PortfolioProject {
  id: number;
  title: string;
  description: string;
  technologies: string[];
  project_url?: string;
  repository_url?: string;
  images: string[];
  start_date: string;
  end_date?: string;
  is_ongoing: boolean;
}

export interface SavedJob {
  id: number;
  candidate_id: number;
  job_id: number;
  saved_at: string;
  notes?: string;
  job: Job;
}

export interface JobAlert {
  id: number;
  candidate_id: number;
  title: string;
  keywords: string[];
  location?: Location;
  employment_types: EmploymentType[];
  experience_levels: ExperienceLevel[];
  salary_range?: SalaryRange;
  remote_policy?: RemotePolicy;
  is_active: boolean;
  email_frequency: 'immediate' | 'daily' | 'weekly';
  last_sent_at?: string;
}

export interface CandidatePreferences {
  job_alerts_enabled: boolean;
  email_notifications: boolean;
  profile_visibility: 'public' | 'private' | 'employers_only';
  allow_recruiter_contact: boolean;
  preferred_contact_time: string;
  job_recommendations_enabled: boolean;
  newsletter_subscription: boolean;
}

export interface Subscription {
  id: number;
  user_id: number;
  plan_id: number;
  status: SubscriptionStatus;
  starts_at: string;
  ends_at: string;
  trial_ends_at?: string;
  jobs_remaining: number;
  featured_jobs_remaining: number;
  applications_limit: number;
  custom_features: string[];
}

export interface BillingInfo {
  id: number;
  user_id: number;
  company_name?: string;
  billing_address: Location;
  tax_id?: string;
  payment_method: PaymentMethod;
  invoice_email?: string;
}

export interface PaymentMethod {
  id: number;
  type: 'card' | 'bank_account' | 'paypal';
  last_four?: string;
  brand?: string;
  expires_at?: string;
  is_default: boolean;
}

export interface TeamMember {
  id: number;
  company_id: number;
  user_id: number;
  role: 'owner' | 'admin' | 'recruiter' | 'member';
  permissions: EmployerPermission[];
  invited_at?: string;
  joined_at?: string;
  status: 'pending' | 'active' | 'inactive';
  user: BaseUser;
}

export interface EmployerPermission {
  id: number;
  name: string;
  slug: string;
  description?: string;
  category: 'jobs' | 'candidates' | 'company' | 'billing' | 'team';
}

export interface AdminPermission {
  id: number;
  name: string;
  slug: string;
  description?: string;
  category: 'users' | 'companies' | 'jobs' | 'system' | 'analytics' | 'settings';
}

export interface AdminActivity {
  id: number;
  admin_id: number;
  action: string;
  description: string;
  target_type?: string;
  target_id?: number;
  ip_address?: string;
  user_agent?: string;
  performed_at: string;
  metadata?: Record<string, any>;
}

export interface ApplicationTimeline {
  id: number;
  application_id: number;
  status: ApplicationStatus;
  changed_by: number;
  notes?: string;
  changed_at: string;
  metadata?: Record<string, any>;
}

export interface OfferDetails {
  salary: SalaryRange;
  start_date: string;
  benefits: string[];
  terms: string;
  expires_at: string;
  response_deadline: string;
  custom_terms?: Record<string, any>;
}

// API Response Types
export interface UserResponse {
  user: Candidate | Employer | Administrator;
  token?: string;
  permissions?: string[];
  preferences?: Record<string, any>;
}

export interface LoginCredentials {
  email: string;
  password: string;
  remember?: boolean;
}

export interface RegisterData {
  email: string;
  password: string;
  password_confirmation: string;
  role: UserRole;
  first_name?: string;
  last_name?: string;
  company_name?: string;
  terms_accepted: boolean;
}

// Form Validation Types
export interface ValidationError {
  field: string;
  message: string;
  code?: string;
}

export interface ApiError {
  message: string;
  errors?: ValidationError[];
  code?: number;
  details?: Record<string, any>;
}

// Utility Types
export type UserWithoutSensitive = Omit<BaseUser, 'email_verified_at' | 'created_at' | 'updated_at'>;

export type CandidatePublicProfile = Pick<CandidateProfile, 
  | 'first_name' 
  | 'last_name' 
  | 'headline' 
  | 'summary' 
  | 'location' 
  | 'skills' 
  | 'experience' 
  | 'education'
> & {
  user: Pick<BaseUser, 'id' | 'profile_image'>;
  portfolio?: Pick<Portfolio, 'id' | 'title' | 'projects'>;
};

export type JobSummary = Pick<Job, 
  | 'id' 
  | 'title' 
  | 'slug' 
  | 'employment_type' 
  | 'experience_level' 
  | 'location' 
  | 'salary_range' 
  | 'posted_at' 
  | 'is_featured'
> & {
  company: Pick<Company, 'id' | 'name' | 'logo' | 'location'>;
};

export type ApplicationSummary = Pick<JobApplication, 
  | 'id' 
  | 'status' 
  | 'applied_at'
> & {
  job: JobSummary;
};

// Component Props Types
export interface UserCardProps {
  user: Candidate | Employer | Administrator;
  showDetails?: boolean;
  showActions?: boolean;
  variant?: 'compact' | 'full' | 'minimal';
}

export interface JobCardProps {
  job: Job | JobSummary;
  showSaveButton?: boolean;
  showApplyButton?: boolean;
  userRole?: UserRole;
  variant?: 'card' | 'list' | 'minimal';
}

export interface DashboardWidgetProps {
  title: string;
  value: string | number;
  change?: number;
  trend?: 'up' | 'down' | 'neutral';
  loading?: boolean;
  variant?: 'metric' | 'chart' | 'list';
} 