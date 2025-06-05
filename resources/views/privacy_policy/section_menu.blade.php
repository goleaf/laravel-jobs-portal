<div class="flex flex-wrap">
    <div class="flex-1 md-3">
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-white shadow rounded-lg overflow-hidden body px-0">
                <ul class="flex space-x-1 nav-pills flex-col">
                    <li class="nav-item">
                        <a href="{{ route('privacy.policy.index', ['section' => 'privacy_policy']) }}"
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ (isset($sectionName) && $sectionName =="privacy_policy') ? 'active' : '' }}">
                            {{ __('messages.setting.privacy_policy') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('privacy.policy.index', ['section' => 'terms_conditions']) }}"
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium {{ (isset($sectionName) && $sectionName =="terms_conditions') ? 'active' : '' }}">
                            {{ __('messages.setting.terms_conditions') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="flex-1 md-9">
        @yield('section')
    </div>
</div>

