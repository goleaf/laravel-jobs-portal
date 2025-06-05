# Product Context - Laravel Job Portal

## Product Overview
**Product Name**: Laravel Job Portal Platform  
**Version**: Laravel 12.17.0 Enterprise  
**Type**: B2B/B2C Job Board Platform  
**Target Market**: Enterprise job placement and recruitment  

## Core Product Features
- **Multi-tenant Job Board**: Support for multiple employers and job seekers
- **Advanced Search & Filtering**: Location, skills, salary range, experience level
- **Application Management**: Complete application lifecycle management
- **Company Profiles**: Rich employer branding and showcase capabilities
- **Candidate Profiles**: Resume management and skill showcase
- **Real-time Notifications**: Job alerts, application updates, messages

## Technical Architecture
- **Framework**: Laravel 12.17.0 with modern PHP 8.3+ features
- **Frontend**: TailwindCSS 3.x with dark mode support
- **Database**: MySQL with optimized indexing for job search
- **Caching**: Redis for performance optimization
- **Queue System**: Laravel Queues for background processing
- **File Storage**: Local/S3 for resume and document management

## User Personas
1. **Job Seekers**: Individuals looking for employment opportunities
2. **Employers**: Companies posting jobs and managing applications
3. **Administrators**: Platform managers with full system access

## Business Logic
- **Job Posting Workflow**: Creation → Review → Publication → Applications → Hiring
- **Application Process**: Search → Apply → Track → Interview → Decision
- **Subscription Model**: Basic/Premium employer packages
- **Revenue Streams**: Job posting fees, premium features, promoted listings

## Current State
- **Security**: Enhanced authentication and authorization in progress
- **UI/UX**: TailwindCSS migration completed
- **Performance**: Optimized for 1000+ concurrent users
- **Testing**: Comprehensive test coverage implementation ongoing
- **Multilingual**: Support for 8+ languages via JSON files 