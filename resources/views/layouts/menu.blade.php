<li class="flex space-x-8-item {{ Request::is("admin/dashboard*') ? 'active' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.dashboard') }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa fa-digital-tachograph"></i></span>
        <span class="aside-menu-title">{{ __('messages.dashboard') }}</span>
    </a>
</li>
<li class="flex space-x-8-item {{ Request::is("admin/employers*','admin/reported-employers*') ? 'active' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('company.index') }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-user-friends"></i></span>
        <span class="aside-menu-title">{{ __('messages.employers') }}</span>
        <span class="hidden">{{ __('messages.employers') }}</span>
        <span class="hidden">{{ __('messages.company.reported_employers') }}</span>
    </a>
</li>
<li class="flex space-x-8-item {{ Request::is("admin/admin*') ? 'active' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.dashboard') }}">
        <span class="aside-menu-icon pe-3"><i class="fa-solid fa-user-tie"></i></span>
        <span class="aside-menu-title">{{ __('messages.candidate.admins') }}</span>
    </a>
</li>
<li class="flex space-x-8-item {{ Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidate*') ? 'active' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.candidates.index') }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-user-circle"></i></span>
        <span class="aside-menu-title">{{ __('messages.candidates') }}</span>
        <span class="hidden">{{ __('messages.candidates') }}</span>
        <span class="hidden">{{ __('messages.required_degree_levels') }}</span>
        <span class="hidden">{{ __('messages.candidate.reported_candidates') }}</span>
        <span class="hidden">{{ __('messages.all_resumes') }}</span>
        <span class="hidden">{{ __('messages.selected_candidate') }}</span>
    </a>
</li>
<li class="flex space-x-8-item {{ Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'active' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.jobs.index') }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-briefcase"></i></span>
        <span class="aside-menu-title">{{ __('messages.jobs') }}</span>
        <span class="hidden">{{ __('messages.jobs') }}</span>
        <span class="hidden">{{ __('messages.job_categories') }}</span>
        <span class="hidden">{{ __('messages.job_types') }}</span>
        <span class="hidden">{{ __('messages.job_tags') }}</span>
        <span class="hidden">{{ __('messages.job_shifts') }}</span>
        <span class="hidden">{{ __('messages.reported_jobs') }}</span>
        <span class="hidden">{{ __('messages.job_notification.job_notifications') }}</span>
        <span class="hidden">{{ __('messages.expired_jobs') }}</span>
    </a>
</li>
{{-- Temporarily disabled - post-categories route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/post-categories*','admin/posts*','admin/post-comments*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('post-categories.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="far fa-list-alt"></i></span>
        <span class="aside-menu-title">{{ __('messages.blogs') }}</span>
        <span class="hidden">{{ __('messages.post_category.post_categories') }}</span>
        <span class="hidden">{{ __('messages.post.posts') }}</span>
        <span class="hidden">{{ __('messages.post_comments') }}</span>
    </a>
</li>
--}}
{{-- Temporarily disabled - plans route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/plans*','admin/transactions*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('plans.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="fab fa-bandcamp"></i></span>
        <span class="aside-menu-title">{{ __('messages.plan.subscriptions') }}</span>
        <span class="hidden">{{ __('messages.post_comments') }}</span>
        <span class="hidden">{{ __('messages.subscriptions_plans') }}</span>
    </a>
</li>
--}}
{{-- Temporarily disabled - subscribers route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/subscribers*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('subscribers.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-bell"></i></span>
        <span class="aside-menu-title">{{ __('messages.subscribers') }}</span>
        <span class="hidden">{{ __('messages.subscribers') }}</span>
        <span class="hidden">{{ __('messages.transactions') }}</span>

    </a>
</li>
--}}
{{-- Temporarily disabled - countries route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/countries*','admin/states*','admin/cities*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.dashboard')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-globe-americas"></i></span>
        <span class="aside-menu-title">{{ __('messages.country.countries') }}</span>
        <span class="hidden">{{ __('messages.country.countries') }}</span>
        <span class="hidden">{{ __('messages.state.states') }}</span>
        <span class="hidden">{{ __('messages.city.cities') }}</span>

    </a>
</li>
--}}
{{-- Temporarily disabled - maritalStatus route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('admin.dashboard')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-life-ring"></i></span>
        <span class="aside-menu-title">{{ __('messages.general') }}</span>
        <span class="hidden">{{ __('messages.marital_statuses') }}</span>
        <span class="hidden">{{ __('messages.skills') }}</span>
        <span class="hidden">{{ __('messages.salary_periods') }}</span>
        <span class="hidden">{{ __('messages.industries') }}</span>
        <span class="hidden">{{ __('messages.company_sizes') }}</span>
        <span class="hidden">{{ __('messages.functional_areas') }}</span>
        <span class="hidden">{{ __('messages.career_levels') }}</span>
        <span class="hidden">{{ __('messages.salary_currencies') }}</span>
        <span class="hidden">{{ __('messages.ownership_types') }}</span>
        <span class="hidden">{{ __('messages.languages') }}</span>

    </a>
</li>
--}}
{{-- Temporarily disabled - noticeboards route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('noticeboards.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-sticky-note"></i></span>
        <span class="aside-menu-title">{{ __('messages.cms') }}</span>
        <span class="hidden">{{ __('messages.noticeboards') }}</span>
        <span class="hidden">{{ __('messages.faq.faq') }}</span>
        <span class="hidden">{{ __('messages.inquires') }}</span>
        <span class="hidden">{{ __('messages.setting.notification_settings') }}</span>
        <span class="hidden">{{ __('messages.setting.privacy_policy') }}</span>
        <span class="hidden">{{ __('messages.setting.front_settings') }}</span>
        <span class="hidden">{{ __('messages.translation_manager') }}</span>
        <span class="hidden">{{ __('messages.email_templates') }}</span>
        <span class="hidden">{{ __('messages.settings') }}</span>

    </a>
</li>
--}}
{{-- Temporarily disabled - testimonials route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/testimonials*','admin/branding-sliders*','admin/header-sliders*','admin/image-sliders*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('testimonials.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-sticky-note"></i></span>
        <span class="aside-menu-title">{{ __('messages.cms_sliders') }}</span>
        <span class="hidden">{{ __('messages.testimonials') }}</span>
        <span class="hidden">{{ __('messages.branding_sliders') }}</span>
        <span class="hidden">{{ __('messages.header_sliders') }}</span>
        <span class="hidden">{{ __('messages.image_sliders') }}</span>

    </a>
</li>
--}}
{{-- Temporarily disabled - cms.services route not implemented yet --}}
{{  --
<li class="flex space-x-8-item  Request::is("admin/cms-services*','admin/cms-about-us*') ? 'active' : '' ">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium flex items-center py-3" aria-current="page" href="{{ route('cms.services.index')  }}">
        <span class="aside-menu-icon pe-3"><i class="fas fa-sticky-note"></i></span>
        <span class="aside-menu-title">{{ __('messages.front_cms') }}</span>
        <span class="hidden">{{ __('messages.cms_services') }}</span>
        <span class="hidden">{{ __('messages.about_us_services') }}</span>

    </a>
</li>
--}}
