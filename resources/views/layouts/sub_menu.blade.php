<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/dashboard*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/dashboard*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/employers*', 'admin/reported-employers*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/employers*') ? 'active' : '' }}"
       href="{{ route('company.index') }}">{{ __('messages.employers') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/employers*', 'admin/reported-employers*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/reported-employers*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.company.reported_employers') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/admins*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/admins*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.candidate.admins') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidates*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/candidates*') ? 'active' : '' }}"
       href="{{ route('candidates.index') }}">{{ __('messages.candidates') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidates*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/degree-levels*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.required_degree_levels') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidates*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/reported-candidates*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.candidate.reported_candidates') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidates*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/resumes*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.all_resumes') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/candidates*','admin/degree-levels*','admin/reported-candidates*','admin/resumes*','admin/selected-candidates*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/selected-candidates*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.selected_candidate') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/jobs*') ? 'active' : '' }}"
       href="{{ route('admin.jobs.index') }}">{{ __('messages.jobs') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/job-categories*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.job_categories') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/job-types*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.job_types') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/job-tags*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.job_tags') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/job-shifts*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.job_shifts') }}</a>
</li>
<div class="{{ !Request::is('admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? ' hidden ' : '' }}">
    <li class="hidden d-xl-grid relative inline-block text-left relative inline-block text-left -hover">
        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center py-3 ps-2" aria-current="page"
           href="javascript:void(0)">
            <span class="horizontal-menu-icon"><i class="fas fa-ellipsis-vertical fs-4"></i></span>
        </a>
        <ul class="horizontal-submenu origin-top-right absolute right-0 mt-2 w-56 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 top-100">
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/reported-jobs*') ? 'active' : '' }} {{ !Request::is('admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}"
                   href="{{ route('reported.jobs') }}">{{ __('messages.reported_jobs') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/job-notification*') ? 'active' : '' }} {{ !Request::is('admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}"
                   href="{{ route('jobnotification.index') }}">{{ __('messages.job_notification.job_notifications') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/expired-jobs*') ? 'active' : '' }} {{ !Request::is('admin/jobs*','admin/job-categories*','admin/job-types*','admin/job-tags*','admin/job-shifts*','admin/reported-jobs*','admin/job-notification*','admin/expired-jobs*') ? 'hidden' : '' }}"
                   href="{{ route('admin.dashboard') }}">{{ __('messages.expired_jobs') }}</a>
            </li>
        </ul>
    </li>
</div>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/post-categories*','admin/posts*','admin/post-comments*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/post-categories*') ? 'active' : '' }}"
       href="{{ route('post-categories.index') }}">{{ __('messages.post_category.post_categories') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/post-categories*','admin/posts*','admin/post-comments*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/posts*') ? 'active' : '' }}"
       href="{{ route('posts.index') }}">{{ __('messages.post.posts') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/post-categories*','admin/posts*','admin/post-comments*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/post-comments*') ? 'active' : '' }}"
       href="{{ route('post.comments') }}">{{ __('messages.post_comments') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/plans*','admin/transactions*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/plans*') ? 'active' : '' }}"
       href="{{ route('plans.index') }}">{{ __('messages.subscriptions_plans') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/plans*','admin/transactions*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/transactions*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.transactions') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/subscribers*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/subscribers*') ? 'active' : '' }}"
       href="{{ route('subscribers.index') }}">{{ __('messages.subscribers') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/countries*','admin/states*','admin/cities*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/countries*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.country.countries') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/countries*','admin/states*','admin/cities*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/states*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.state.states') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/countries*','admin/states*','admin/cities*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/cities*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.city.cities') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/marital-status*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.marital_statuses') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/skills*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.skills') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/salary-periods*') ? 'active' : '' }}"
       href="{{ route('salaryPeriod.index') }}">{{ __('messages.salary_periods') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/industries*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.industries') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/company-sizes*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.company_sizes') }}</a>
</li>
<div class="{{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? ' hidden ' : '' }}">
    <li class="hidden d-xl-grid relative inline-block text-left relative inline-block text-left -hover">
        <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center py-3 ps-2" aria-current="page"
           href="javascript:void(0)">
            <span class="horizontal-menu-icon"><i class="fas fa-ellipsis-vertical fs-4"></i></span>
        </a>
        <ul class="horizontal-submenu origin-top-right absolute right-0 mt-2 w-56 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 top-100">
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/functional-areas*') ? 'active' : '' }}{{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}"
                   href="{{ route('functionalArea.index') }}">{{ __('messages.functional_areas') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/career-levels*') ? 'active' : '' }} {{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}"
                   href="{{ route('admin.dashboard') }}">{{ __('messages.career_levels') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/salary-currencies*') ? 'active' : '' }} {{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}"
                   href="{{ route('salaryCurrency.index') }}">{{ __('messages.salary_currencies') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/ownership-types*') ? 'active' : '' }} {{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}"
                   href="{{ route('ownerShipType.index') }}">{{ __('messages.ownership_types') }}</a>
            </li>
            <li>
                <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/languages*') ? 'active' : '' }} {{ !Request::is('admin/marital-status*','admin/skills*','admin/salary-periods*','admin/industries*','admin/company-sizes*','admin/functional-areas*','admin/career-levels*','admin/salary-currencies*','admin/ownership-types*','admin/languages*') ? 'hidden' : '' }}"
                   href="{{ route('admin.dashboard') }}">{{ __('messages.languages') }}</a>
            </li>
        </ul>
    </li>
</div>

<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/noticeboards*') ? 'active' : '' }}"
       href="{{ route('noticeboards.index') }}">{{ __('messages.noticeboards') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/faqs*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.faq.faq') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/inquires*') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">{{ __('messages.inquires') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mt-3 mb-xl-0 {{ !Request::is("admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/notification-settings*') ? 'active' : '' }}"
       href="{{ route('jobnotification.index') }}">{{ __('messages.setting.notification_settings') }}</a>
</li>
<div class="{{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? ' hidden ' : '' }}">
<li class="hidden d-xl-grid relative inline-block text-left relative inline-block text-left -hover">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium flex items-center py-3 ps-2" aria-current="page"
       href="javascript:void(0)">
        <span class="horizontal-menu-icon"><i class="fas fa-ellipsis-vertical fs-4"></i></span>
    </a>
    <ul class="horizontal-submenu origin-top-right absolute right-0 mt-2 w-56 rounded -md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 top-100">
        <li>
            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/privacy-policy*') ? 'active' : '' }} {{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}"
               href="{{ route('privacy.policy.index') }}">{{ __('messages.setting.privacy_policy') }}</a>
        </li>
        <li>
            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/front-settings*') ? 'active' : '' }} {{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}"
               href="{{ route('admin.dashboard') }}">{{ __('messages.setting.front_settings') }}</a>
        </li>
        {{  -- <li>
            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100  Request::is("admin/translation-manager*') ? 'active' : ''  {{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/translation-manager*','admin/email-template*','admin/settings*') ? 'hidden' : ''  }}"
               href="{{ route('translation-manager.index') }}">{{ __('messages.translation_manager') }}</a>
        </li> --}}
        <li>
            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/email-template*') ? 'active' : '' }} {{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}"
               href="{{ route('admin.dashboard') }}">{{ __('messages.email_templates') }}</a>
        </li>
        <li>
            <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ Request::is("admin/settings*') ? 'active' : '' }} {{ !Request::is('admin/noticeboards*','admin/faqs*','admin/inquires*','admin/notification-settings*','admin/privacy-policy*','admin/front-settings*','admin/email-template*','admin/settings*') ? 'hidden' : '' }}"
               href="{{ route('admin.dashboard') }}">{{ __('messages.settings') }}</a>
        </li>
    </ul>
</li>
</div>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/testimonials*','admin/branding-sliders*','admin/header-sliders*','admin/image-sliders*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/testimonials*') ? 'active' : '' }}"
       href="{{ route('testimonials.index') }}">{{ __('messages.testimonials') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/testimonials*','admin/branding-sliders*','admin/header-sliders*','admin/image-sliders*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/branding-sliders*') ? 'active' : '' }}"
       href="{{ route('branding.sliders.index') }}">{{ __('messages.branding_sliders') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/testimonials*','admin/branding-sliders*','admin/header-sliders*','admin/image-sliders*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/header-sliders*') ? 'active' : '' }}"
       href="{{ route('header.sliders.index') }}">{{ __('messages.header_sliders') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/testimonials*','admin/branding-sliders*','admin/header-sliders*','admin/image-sliders*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/image-sliders*') ? 'active' : '' }}"
       href="{{ route('image-sliders.index') }}">{{ __('messages.image_sliders') }}</a>
</li>

<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/cms-services*','admin/cms-about-us*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/cms-services*') ? 'active' : '' }}"
       href="{{ route('cms.services.index') }}">{{ __('messages.cms_services') }}</a>
</li>
<li class="flex space-x-8-item relative mx-xl-3 mb-3 mb-xl-0 {{ !Request::is("admin/cms-services*','admin/cms-about-us*') ? 'hidden' : '' }}">
    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded -md text-sm font-medium p-0 {{ Request::is("admin/cms-about-us*') ? 'active' : '' }}"
       href="{{ route('cms.about-us.service') }}">{{ __('messages.about_us_services') }}</a>
</li>
