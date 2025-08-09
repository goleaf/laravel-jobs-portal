# Task: Full Migration to Filament (Frontend + Backend)

## Description
Migrate the entire application UI and backend management to Filament v3 using MCP-driven planning. Replace all legacy Blade views, controllers-driven pages, and custom UI components with Filament Panels, Resources, Pages, Widgets, Forms, and Tables. Remove unused/legacy files post-migration.

## Complexity
Level: 4
Type: Complex System Migration

## Technology Stack
- Framework: Laravel 12.x
- Admin/UI Framework: Filament v3 (Panels, Forms, Tables, Widgets)
- Realtime: Livewire v3 (bundled with Filament)
- Styling: TailwindCSS (Filament presets)
- Icons: Blade Heroicons (via Filament) and/or Blade UI Kit
- Auth: Minimal Admin guard for panel access; Public panel with guest-only pages
- Storage: Eloquent ORM, existing DB schema preserved

## Technology Validation Checkpoints
- [ ] Require packages: `filament/filament`, `filament/forms`, `filament/tables`
- [ ] Publish Filament config and assets
- [ ] Create base Admin Panel; verify dashboard loads
- [ ] Create Public Panel; verify guest page loads
- [ ] Build minimal Hello World resource and page; verify navigation and theming
- [ ] Confirm Livewire v3 + Tailwind build works with Vite

## Status
- [x] Initialization complete
- [x] Planning complete
- [ ] Technology validation complete
- [ ] Implementation in progress

## Implementation Plan (Phased)

1) Dependencies & Setup
- Add packages: `composer require filament/filament:^3 filament/forms:^3 filament/tables:^3`
- Optionally add: `filament/spatie-laravel-translatable-plugin`, `spatie/laravel-permission` (if needed later)
- Publish config/theme: `php artisan filament:install`
- Validate Livewire v3 and Vite config

2) Panels
- Create `AdminPanel` at `/admin` (default theme, minimal auth)
- Create `PublicPanel` at `/` with guest access pages for Jobs and Companies browsing
- Configure panels’ navigation, locale, dark mode, primary color

3) Domain Resources (Admin)
- Create Filament Resources with Tables + Forms:
  - CompanyResource (companies)
  - JobResource (jobs)
  - IndustryResource (industries)
  - CompanySizeResource (company_sizes)
  - CityResource (cities)
  - PlanResource (plans)
  - TransactionResource (transactions)
  - SubscriptionResource (subscriptions)
  - Any other master data used by listings/filters
- Actions: bulk actions, soft-deletes, filters, advanced search
- Media: integrate uploads via Filament Forms where used

4) Public Pages (Guest, SEO-friendly)
- `JobsIndexPage` (filters, pagination, cards/list toggle)
- `JobShowPage`
- `CompaniesIndexPage` (filters, pagination, grid/list toggle)
- `CompanyShowPage`
- Shared widgets: featured companies, quick search, breadcrumbs

5) Business Logic & Queries
- Move controller/filter logic into:
  - Eloquent scopes
  - Filament Table filters and query builders
  - Form schemas and validation rules
- Replace `x-icon`/custom Blade components with Filament/heroicons

6) Routing & Navigation
- Remove legacy Blade route endpoints replaced by Filament panels
- Keep redirects for major legacy URLs → new public panel pages
- Remove or migrate API endpoints used only by views to Filament actions/pages

7) Localization
- Keep current JSON translations; map strings into Filament page/resource labels and actions
- Ensure language middleware compatible with Filament requests

8) Cleanup (Remove Not Usable Files)
- Remove legacy Blade views under `resources/views/**` that are fully replaced
- Remove unused controllers, requests, and routes
- Remove custom UI components superseded by Filament components
- Remove icon view overrides causing `View [components.outline.list] not found`

9) Testing & QA
- Add feature tests for admin resources (index, create, edit, delete)
- Add feature tests for public pages (filters, pagination, SEO meta)
- Minimal snapshot tests for table columns/filters
- Run CI: phpunit + pint + phpstan; fix issues

## Creative Phases Required
- [ ] Public Panel information architecture and navigation (UI/UX design)
- [ ] Public list/grid card designs in Filament pages
- [ ] Filters UX (chips, active filter bar) in Filament Tables

## Dependencies
- Laravel 12 + PHP 8.2 compatible Filament packages
- Existing models: `Company`, `Job`, `Industry`, `CompanySize`, `City`, etc.
- Tailwind configured via Vite

## Challenges & Mitigations
- Filament auth vs. prior auth removal: use minimal admin-only guard, public panel is guest-only
- Large view cleanup: delete in phases post-acceptance; keep backups under `archive/`
- Icon/component mismatches: standardize on Filament icons and remove custom `x-icon`
- Query performance: leverage existing scopes, indexes; paginate appropriately

## Detailed Steps
1. Install Filament and publish config
2. Generate AdminPanel and dashboard; verify loads
3. Generate PublicPanel and a Hello World page; verify guest access
4. Scaffold CompanyResource and JobResource with fields/relations
5. Implement filters/sorts matching legacy behavior
6. Build CompaniesIndexPage/ShowPage
7. Build JobsIndexPage/ShowPage
8. Move featured companies widget into Filament widget
9. Migrate translations to Filament labels
10. Replace routes with panel routes; add redirects
11. Remove legacy Blade views/components superseded by panels
12. Write tests and run QA
13. Scaffold PlanResource, TransactionResource, and SubscriptionResource
    - Forms: capture required fields (e.g., plans: name, price, duration; transactions: amount, currency, gateway, status; subscriptions: user/company, plan, start/end, status)
    - Tables: columns, advanced filters (status, gateway, date range), bulk actions; add export if needed
    - Relations: Subscription -> Plan, Subscription -> Company/User; Transaction -> Subscription/Company as applicable
    - Actions: refund/cancel/resubscribe where applicable using Filament Actions
    - Policies: restrict to admin panel guard; integrate with role/permission if enabled
    - Tests: feature tests for listing, create, edit; policy checks

## Testing Strategy
- HTTP tests for panel access and page rendering
- Table filter assertions for admin and public lists
- Form validation tests for admin create/update
- Lighthouse/SEO checks for public pages

---

## Legacy Tasks (Superseded by Filament Migration)
- Controller refactor → handled by Filament Resources/Pages
- Multi-language system → retained via JSON translations; mapped into Filament
- Blade & UI refactor → replaced by Filament + Tailwind theme
- Testing → covered in new test plan
- Data & Auth cleanup → minimize auth to admin panel only; public panel guest
