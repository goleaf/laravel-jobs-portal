<div class="">
    <div class="pb-0 pt-2">
        <div class="overflow-auto flex">
            <ul class="border flex-nowrap border border-gray-300 -gray-300 flex space-x-1 nav-stretch nav-line-tabs nav-line-tabs-2x -transparent fs-5">
                <li class="">
                    <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="general' || Request::is('settings*')) ? 'text-indigo-600' : '' }}"
                       href="{{ route('admin.dashboard', ['section' => 'general']) }}">{{ __('messages.general') }}</a>
                </li>
                <li class="">
                    <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="front_office_details') ? 'text-indigo-600' : '' }}"
                       href="{{ route('admin.dashboard', ['section' => 'front_office_details']) }}">  {{ __('messages.footer_settings') }}</a>
                </li>
                <li class="">
                    <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="social_settings') ? 'text-indigo-600' : '' }}"
                       href="{{ route('admin.dashboard', ['section' => 'social_settings']) }}">  {{ __('messages.social_settings') }}</a>
                </li>
                <li class="">
                    <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="about_us') ? 'text-indigo-600' : '' }}"
                       href="{{ route('admin.dashboard', ['section' => 'about_us']) }}"> {{ __('messages.about_us') }}</a>
                </li>
                <li class="">
                    <a class="rounded text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium text-active-primary me-6 {{ (isset($sectionName) && $sectionName =="env_setting') ? 'text-indigo-600' : '' }}"
                       href="{{ route('admin.dashboard', ['section' => 'env_setting']) }}"> {{ __('messages.env') }}</a>
                </li>
            </ul>
        </div>
    </div>
</div>



