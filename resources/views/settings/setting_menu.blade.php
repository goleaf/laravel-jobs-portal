<div class="">
    <div class="pt-2 pb-0">
        <div class="flex overflow-auto">
            <ul class="flex space-x-1 nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 flex-nowrap">
                <li class="nav-item">
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="general' || Request::is('settings*')) ? 'text-primary' : ''}}"
                       href="{{ route('admin.dashboard', ['section' => 'general']) }}">{{ __('messages.general') }}</a>
                </li>
                <li class="nav-item">
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="front_office_details') ? 'text-primary' : ''}}"
                       href="{{ route('admin.dashboard', ['section' => 'front_office_details']) }}">  {{ __('messages.footer_settings') }}</a>
                </li>
                <li class="nav-item">
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="social_settings') ? 'text-primary' : ''}}"
                       href="{{ route('admin.dashboard', ['section' => 'social_settings']) }}">  {{ __('messages.social_settings') }}</a>
                </li>
                <li class="nav-item">
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="about_us') ? 'text-primary' : ''}}"
                       href="{{ route('admin.dashboard', ['section' => 'about_us']) }}"> {{ __('messages.about_us') }}</a>
                </li>
                <li class="nav-item">
                    <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="env_setting') ? 'text-primary' : ''}}"
                       href="{{ route('admin.dashboard', ['section' => 'env_setting']) }}"> {{ __('messages.env') }}</a>
                </li>
            </ul>
        </div>
    </div>
</div>



