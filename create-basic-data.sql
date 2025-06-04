-- Create basic reference data and test company
-- This script creates minimal data needed for testing company functionality

-- Insert basic industries
INSERT IGNORE INTO industries (id, name, is_default, created_at, updated_at) VALUES 
(1, 'Technology', 1, NOW(), NOW()),
(2, 'Healthcare', 1, NOW(), NOW()),
(3, 'Finance', 1, NOW(), NOW()),
(4, 'Education', 1, NOW(), NOW()),
(5, 'Manufacturing', 1, NOW(), NOW());

-- Insert basic ownership types  
INSERT IGNORE INTO ownership_types (id, name, created_at, updated_at) VALUES
(1, 'Private', NOW(), NOW()),
(2, 'Public', NOW(), NOW()),
(3, 'Government', NOW(), NOW()),
(4, 'Non-Profit', NOW(), NOW()),
(5, 'Partnership', NOW(), NOW());

-- Insert basic company sizes
INSERT IGNORE INTO company_sizes (id, size, created_at, updated_at) VALUES
(1, '1-10', NOW(), NOW()),
(2, '11-50', NOW(), NOW()),
(3, '51-200', NOW(), NOW()),
(4, '201-500', NOW(), NOW()),
(5, '500+', NOW(), NOW());

-- Insert test users
INSERT IGNORE INTO users (id, first_name, last_name, email, email_verified_at, password, created_at, updated_at) VALUES
(1, 'Test', 'Company Owner', 'test-company-1@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(2, 'Demo', 'Employer', 'demo-employer@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
(3, 'Sample', 'Business Owner', 'sample-business@example.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- Insert test companies
INSERT IGNORE INTO companies (id, user_id, ceo, industry_id, ownership_type_id, company_size_id, established_in, details, website, location, no_of_offices, unique_id, created_at, updated_at) VALUES
(1, 1, 'John Smith', 1, 1, 2, 2020, 'TechCorp is a leading technology company specializing in innovative software solutions. We provide cutting-edge development services and consulting to businesses worldwide.', 'https://techcorp.example.com', 'San Francisco, CA', 3, 'techcorp-2024', NOW(), NOW()),
(2, 2, 'Sarah Johnson', 2, 1, 3, 2018, 'HealthPlus is a healthcare technology company focused on improving patient care through digital solutions. We develop medical software and health monitoring systems.', 'https://healthplus.example.com', 'New York, NY', 5, 'healthplus-2024', NOW(), NOW()),
(3, 3, 'Michael Davis', 3, 2, 4, 2015, 'FinanceFlow provides comprehensive financial services and consulting to small and medium businesses. Our expertise spans accounting, tax planning, and investment advisory.', 'https://financeflow.example.com', 'Chicago, IL', 8, 'financeflow-2024', NOW(), NOW());

-- Show created data
SELECT 'Companies created:' as info;
SELECT c.id, c.ceo, u.first_name, u.last_name, u.email, i.name as industry, o.name as ownership_type, cs.size as company_size
FROM companies c 
LEFT JOIN users u ON c.user_id = u.id
LEFT JOIN industries i ON c.industry_id = i.id  
LEFT JOIN ownership_types o ON c.ownership_type_id = o.id
LEFT JOIN company_sizes cs ON c.company_size_id = cs.id
LIMIT 5; 