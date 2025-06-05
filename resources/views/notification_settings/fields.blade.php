<div class="flex flex-wrap">
    @foreach($notificationSetting as $type => $settings)
        <div class="md:w-full lg:w-4/12 px-2 flex-1 sm-12">
            <h5>{{ __('messages.notification_settings.'.$type) }}</h5>
            <div class="separator my-3"></div>
            <div class="ml-sm-0 mt-4 notification-setting">
                <div class="">
                    @foreach($settings as $key => $value)
                        <div
                                class="lg:w-full px-2 flex-1 md-6 mb-5 flex justify-start flex items-center form-switch">
                            <label class="mt-2 me-2">
                                <input type="checkbox" name="{{ $value->key }}"
                                       class="flex items-center input"
                                       {{ ($value->value == 1) ? 'checked' : '' }} value="{{ $value->key }}">
                                <span class=""></span>
                            </label>
                            <span class="mt-2 fs-5 text-gray-600">{{ __('messages.notification_settings.'.$value->key) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
@endforeach
<!-- Submit Field -->
    <div class="separator my-5"></div>
    <div class="flex justify-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id' => 'jobsSaveBtn']) }}
        <a href="{{ route('notification.settings.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</a>
    </div>
</div>
