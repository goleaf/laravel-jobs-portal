# Job Portal System - Comprehensive Documentation

## Overview

This is a comprehensive job portal system built with Laravel (backend) and Vue.js (frontend). The system provides a complete solution for job posting, searching, and application management with advanced features like real-time notifications, multi-language support, and AI-powered search.

## System Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │    Backend      │    │   Database      │
│   (Vue.js 3)    │◄──►│   (Laravel 12)  │◄──►│   (MySQL)       │
│   TypeScript    │    │   PHP 8.2+      │    │   Redis Cache   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Technology Stack

### Backend
- **Framework**: Laravel 12
- **Language**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Cache**: Redis
- **Authentication**: Laravel Sanctum
- **Testing**: PHPUnit

### Frontend
- **Framework**: Vue.js 3
- **Language**: TypeScript
- **Build Tool**: Vite
- **Styling**: Tailwind CSS
- **Testing**: Vitest + Playwright

### DevOps & Tools
- **Version Control**: Git
- **Package Manager**: Composer (PHP), npm (Node.js)
- **Code Quality**: PHPStan, ESLint, Prettier
- **CI/CD**: GitHub Actions

## Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Redis

### Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd job-portal-system
```

2. **Install backend dependencies**
```bash
composer install
```

3. **Install frontend dependencies**
```bash
cd frontend
npm install
```

4. **Environment setup**
```bash
# Backend
cp .env.example .env
php artisan key:generate

# Frontend
cd frontend
cp .env.example .env
```

5. **Database setup**
```bash
php artisan migrate
php artisan db:seed
```

6. **Start development servers**
```bash
# Backend (in root directory)
php artisan serve

# Frontend (in frontend directory)
npm run dev
```

## Documentation Structure

### 📚 [API Documentation](API_DOCUMENTATION.md)
Comprehensive documentation of all public APIs, endpoints, and authentication flows.

**Includes:**
- Authentication endpoints
- Job management APIs
- Company management APIs
- User management APIs
- File upload APIs
- Real-time features
- Error handling
- Rate limiting

### 🎨 [Frontend Components](FRONTEND_COMPONENTS_DOCUMENTATION.md)
Detailed documentation of all Vue.js components, their props, events, and usage examples.

**Includes:**
- Component architecture
- Props and events documentation
- TypeScript integration
- Performance optimization
- Accessibility guidelines
- Testing strategies

### ⚙️ [Backend Services](BACKEND_SERVICES_DOCUMENTATION.md)
Comprehensive documentation of all backend services, models, and business logic.

**Includes:**
- Service layer architecture
- Job management services
- Company management services
- User management services
- Caching strategies
- Security services
- Performance optimization

## Core Features

### 🔐 Authentication & Authorization
- Laravel Sanctum for SPA authentication
- Role-based access control
- Two-factor authentication
- Password reset functionality
- Email verification

### 💼 Job Management
- Job posting and editing
- Advanced search with filters
- Job categories and types
- Salary range filtering
- Location-based search
- Skills matching
- Featured jobs system

### 🏢 Company Management
- Company profiles
- Company size and industry
- Featured companies
- Company verification
- Analytics dashboard

### 👤 User Management
- User profiles
- Candidate profiles
- Employer profiles
- Application tracking
- Activity history

### 🔍 Advanced Search
- AI-powered job search
- Skills-based matching
- Location-based search
- Salary range filtering
- Experience level filtering
- Remote work options

### 📊 Analytics & Reporting
- Job application analytics
- User activity tracking
- Performance metrics
- SEO analytics
- Business intelligence

### 🌐 Multi-language Support
- Internationalization (i18n)
- RTL language support
- Dynamic translation management
- Locale switching

### 🔔 Real-time Features
- Real-time notifications
- Live chat support
- Application status updates
- Job alerts

### 📱 File Management
- Resume upload
- Company logo upload
- Image optimization
- File preview
- Secure file storage

## API Endpoints Overview

### Authentication
```
POST   /api/v1/auth/login
POST   /api/v1/auth/register
GET    /api/v1/auth/user
POST   /api/v1/auth/logout
POST   /api/v1/auth/logout-all
POST   /api/v1/auth/refresh
GET    /api/v1/auth/check-role/{role}
```

### Jobs
```
GET    /api/v1/jobs
POST   /api/v1/jobs
GET    /api/v1/jobs/{id}
PUT    /api/v1/jobs/{id}
DELETE /api/v1/jobs/{id}
```

### Companies
```
GET    /api/v1/companies
POST   /api/v1/companies
GET    /api/v1/companies/{id}
PUT    /api/v1/companies/{id}
DELETE /api/v1/companies/{id}
```

### Candidates
```
GET    /api/v1/candidates
POST   /api/v1/candidates
GET    /api/v1/candidates/{id}
PUT    /api/v1/candidates/{id}
DELETE /api/v1/candidates/{id}
```

### Applications
```
GET    /api/v1/applications
POST   /api/v1/applications
GET    /api/v1/applications/{id}
PUT    /api/v1/applications/{id}
DELETE /api/v1/applications/{id}
```

## Development Guidelines

### Code Style
- **PHP**: PSR-12 coding standards
- **JavaScript/TypeScript**: ESLint + Prettier
- **Vue**: Vue.js style guide
- **CSS**: Tailwind CSS utility classes

### Testing
- **Backend**: PHPUnit for unit and feature tests
- **Frontend**: Vitest for unit tests, Playwright for E2E tests
- **API**: Postman collections for API testing

### Performance
- Redis caching for frequently accessed data
- Database query optimization
- Image optimization and lazy loading
- CDN integration for static assets

### Security
- Input validation and sanitization
- CSRF protection
- XSS prevention
- SQL injection prevention
- Rate limiting
- Secure file uploads

## Deployment

### Production Checklist
- [ ] Environment variables configured
- [ ] Database migrations run
- [ ] Cache cleared and optimized
- [ ] File permissions set correctly
- [ ] SSL certificate installed
- [ ] CDN configured
- [ ] Monitoring tools set up
- [ ] Backup strategy implemented

### Docker Support
```bash
# Build and run with Docker
docker-compose up -d
```

### Environment Variables
```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=job_portal
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=sync
```

## Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Write tests
5. Submit a pull request

### Commit Message Format
```
type(scope): description

feat(auth): add two-factor authentication
fix(jobs): resolve search filter issue
docs(api): update authentication documentation
```

## Support

### Getting Help
- **Documentation**: Check the detailed documentation files
- **Issues**: Create an issue on GitHub
- **Discussions**: Use GitHub Discussions for questions

### Common Issues
- **Database connection**: Check `.env` configuration
- **Cache issues**: Run `php artisan cache:clear`
- **File permissions**: Ensure storage directory is writable
- **Frontend build**: Check Node.js version and dependencies

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- Laravel team for the excellent framework
- Vue.js team for the progressive JavaScript framework
- Tailwind CSS for the utility-first CSS framework
- All contributors and maintainers

---

**Last Updated**: January 2024  
**Version**: 1.0.0  
**Maintainers**: Development Team