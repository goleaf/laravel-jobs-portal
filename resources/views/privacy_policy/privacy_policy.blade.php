{{ Form::open(['route' => 'privacy.policy.update', 'id' => 'policyTerms']) }}
<div class="flex flex-wrap">
    <div class="my-6">
        {{ Form::label('privacy_policy', __('messages.setting.privacy_policy').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ --  Form::textarea('privacy_policy', $privacyPolicy['privacy_policy'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'id' => 'descriptionPolicy'])  -- }}
        <div id="addPrivacyPolicyDescriptionQuillData"></div>
        {{ Form::hidden('privacy_policy', null, ['id' => 'privacyData']) }}
        <br>
    </div>
</div>
<div class="flex flex-wrap">
    <div class="my-6">
        {{ Form::label('terms_conditions', __('messages.setting.terms_conditions').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ --  Form::textarea('terms_conditions', $privacyPolicy['terms_conditions'], ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-75', 'id' => 'descriptionTerms'])  -- }}
        <div id="addTermConditionDescriptionQuillData"></div>
        {{ Form::hidden('terms_conditions', null, ['id' => 'termData']) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors']) }}
    </div>
{{ Form::close() }}
