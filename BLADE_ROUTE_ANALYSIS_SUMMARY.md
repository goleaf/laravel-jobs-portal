# Blade Route Analysis Summary

**Generated:** 2025-06-04 01:01:56

## 📊 Statistics

| Metric | Count |
|--------|-------|
| Total Blade Files | 1078 |
| Total Route References | 1049 |
| Unique Routes | 170 |
| Missing Routes | 28 |
| Valid Routes | 1021 |
| Success Rate | 97.33% |

## ❌ Missing Routes (Top 20)

| Route Name | Occurrences | Files |
|------------|-------------|-------|
| `front.report-candidate` | 4 | resources/views/front_web/candidate/candidate_details.blade.php, resources/views/front_web_template/candidate/candidate_details.blade.php, resources/views/front_web/candidate/candidate_details.blade.php... +1 more |
| `front.job-categories` | 4 | resources/views/front_web/home/home.blade.php, resources/views/front_web_template/home/home.blade.php, resources/views/front_web/home/home.blade.php... +1 more |
| `front.search-jobs` | 2 | resources/views/front_web/home/home.blade.php, resources/views/front_web/home/home.blade.php |
| `front.contact.send` | 2 | resources/views/front_web/contact/index.blade.php, resources/views/front_web/contact/index.blade.php |
| `admin.email-template.index` | 2 | resources/views/email_templates/edit.blade.php, resources/views/layouts/sub_menu.blade.php |
| `notification.settings.index` | 2 | resources/views/notification_settings/fields.blade.php, resources/views/layouts/sub_menu.blade.php |
| `cms.services.index` | 2 | resources/views/layouts/sub_menu.blade.php, resources/views/layouts/menu.blade.php |
| `reported.jobs` | 1 | resources/views/layouts/sub_menu.blade.php |
| `post.comments` | 1 | resources/views/layouts/sub_menu.blade.php |
| `salaryPeriod.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `functionalArea.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `salaryCurrency.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `ownerShipType.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `branding.sliders.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `header.sliders.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `image-sliders.index` | 1 | resources/views/layouts/sub_menu.blade.php |
| `cms.about-us.service` | 1 | resources/views/layouts/sub_menu.blade.php |

## 🎯 Next Steps

1. **Fix Missing Routes**: Add route definitions for the missing routes listed above
2. **Verify Controllers**: Ensure all controller methods exist for the routes
3. **Test Routes**: Manually test critical routes in browser
4. **Update Middleware**: Verify authentication and authorization requirements
5. **Run Tests**: Execute feature tests to validate route functionality

## 📋 Route Categories to Fix

Based on the missing routes, focus on these areas:

### Admin Routes
- `admin.email-template.index`

### Frontend Routes
- `front.report-candidate`
- `front.job-categories`
- `front.search-jobs`
- `front.contact.send`

### Other Routes
- `notification.settings.index`
- `cms.services.index`
- `reported.jobs`
- `post.comments`
- `salaryPeriod.index`
- `functionalArea.index`
- `salaryCurrency.index`
- `ownerShipType.index`
- `branding.sliders.index`
- `header.sliders.index`
- ... and 2 more

