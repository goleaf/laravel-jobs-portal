# JobPortal Upgrade Plan - Working System, Fully Tested

## P0 – Frontend Stability & CSP Compliance
- [ ] Remove all inline <script>, <style>, and inline event handlers from Blade files
  - [ ] companies/show.blade.php (externalize follow/unfollow, tracking)
  - [ ] jobs/index.blade.php (completed: view toggle/filter toggles)
  - [ ] jobs/show.blade.php (completed: apply/save/share/report hooks; wire JS)
  - [ ] messaging/index.blade.php (externalize auto-scroll, send message, switches)
  - [ ] admin/analytics.blade.php (externalize chart init, refresh)
  - [ ] components/ui/* (remove all embedded <script> blocks)
  - [ ] errors/404, 500, 503 (500 done; complete others)
- [ ] Replace all onclick/onchange with data-action hooks; handle in resources/js/app.js or page modules
- [ ] Ensure all scripts/styles are loaded via Vite only; no CDN or inline
- [ ] Rebuild assets after changes (npm run build)

## P0 – Layout Unification & Assets
- [x] Single layout `layouts/app.blade.php`
- [x] Add Vite JS entry `resources/js/app.js`
- [ ] Ensure all pages/components import only Tailwind classes (no Bootstrap)
- [ ] Replace any `mix()` references with Vite `@vite` or `asset()` built outputs consistently

## P0 – Remove Livewire & Auth Footprints
- [ ] Remove any Livewire references in `resources/js/components/index.js` and blades
- [x] Remove all auth/user code, routes, requests, tests, seeders, factories
- [ ] Purge leftover references (grep for: Livewire|auth|User|login|register)

## P1 – JS Modules (Externalized Page Logic)
- [ ] resources/js/pages/companies/index.js (filters, view toggles, mobile modal, tracking)
- [ ] resources/js/pages/companies/show.js (follow/unfollow, view tracking)
- [ ] resources/js/pages/jobs/index.js (filters, view toggles, mobile modal)
- [ ] resources/js/pages/jobs/show.js (apply/save/share/report handlers)
- [ ] resources/js/pages/messaging/index.js (auto-scroll, send, switch)
- [ ] resources/js/pages/admin/analytics.js (charts & refresh)
- [ ] Wire page modules via @stack('head') or route-name body data attribute

## P1 – Blade Refactor to Components
- [ ] Maximize Blade components use for buttons, inputs, cards, modals
- [ ] Minimize component count by consolidating small variants
- [ ] Ensure all strings use translation functions; fill missing JSON keys

## P1 – Routes & Blades Audit
- [ ] Audit all CRUD blades for jobs, companies, candidates
- [ ] Ensure admin/management blades exist for backend features
- [ ] Verify routes map to blades; add missing routes; browser test navigation

## P2 – Backend Refactor to Form Requests
- [ ] Create `app/Http/Requests/Frontend/*` and `Backend/*` subfolders
- [ ] Convert all web controllers to use dedicated Form Requests
- [ ] Convert all API controllers to use dedicated Form Requests
- [ ] Validation messages: use lang JSON; add missing keys

## P2 – Tests
- [ ] Restructure tests into `tests/Feature/Frontend/*` and `tests/Feature/Backend/*`
- [ ] Add controller tests for all endpoints (web + api)
- [ ] Add form request validation tests
- [ ] Add basic view tests for main pages render
- [ ] Run test suite and fix failures

## P2 – Data
- [ ] Ensure all data via factories/seeders (review existing, remove user-related)
- [ ] Add factories for any missing entities
- [ ] Seed demo data for browser testing

## P3 – Performance & QA
- [ ] Run Pint & PHPStan
- [ ] Optimize queries (N+1 checks)
- [ ] Lighthouse pass for main pages
- [ ] Final CSP sweep (no inline)

## Commands
- Build assets: `npm run build`
- Run tests: `vendor/bin/phpunit`
- Static checks: `./vendor/bin/pint --test`, `./vendor/bin/phpstan analyse`

## Tracking
- [ ] P0 Frontend CSP & Layout
- [ ] P0 Remove Livewire/Auth remnants
- [ ] P1 JS Modules wired
- [ ] P1 Blades & Components
- [ ] P1 Routes & CRUD pages
- [ ] P2 Requests & Validation
- [ ] P2 Tests (controllers/requests/views)
- [ ] P2 Data via factories/seeders
- [ ] P3 Performance & QA
