{{ Form::open(['id'=>'editGeneralForm']) }}
<div class="flex-wrap flex">
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('first_name',__('messages.candidate.first_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('first_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','id'=> 'first_name','placeholder'=> __('messages.candidate.first_name')]) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('last_name',__('messages.candidate.last_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('last_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','id'=>'last_name','placeholder'=> __('messages.candidate.last_name')]) }}
    </div>
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('email',__('messages.candidate.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('email', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','text-red-500','id'=>'email','disabled']) }}
    </div>
    {{-- <div class="mb-5 mb-4 flex-1 sm-6"> --}}
    {{-- {{ Form::label('phone',__('messages.candidate.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 fs-6 fw-bolder text-gray-700 mb-3']) }} --}}
    {{-- {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','id'=>'phone','placeholder'=>__('messages.candidate.phone')]) }} --}}
    {{-- </div> --}}
    <div class="mb-5 flex-1 sm-6">
        {{ Form::label('phone',__('messages.candidate.phone').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="mb-5 flex-1 sm-12">
            {{ Form::tel('phone',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phone','placeholder'=>__('messages.candidate.phone')]) }}
        </div>
    </div>
    <div class="mb-5 flex-1 sm-12">
        {{ Form::label('skillId',__('messages.candidate.candidate_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('candidateSkills[]',$data['skills'], (count($candidateSkills) > 0)?$candidateSkills:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','id'=>'skillId','multiple'=>true,'text-red-500']) }}
    </div>

    <div class="mb-5 flex-1 sm-12">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'candidateCountryId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_country'),'text-red-500']) }}
    </div>
    <div class="mb-5 flex-1 sm-12">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [],  null, ['id'=>'candidateStateId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','placeholder' => __('messages.company.select_state')]) }}
    </div>
    <div class="mb-5 flex-1 sm-12">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [],  null, ['id'=>'candidateCityId','class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','placeholder' => __('messages.company.select_city')]) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200EditGeneralSave','data-loading-text'=>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span>".__('messages.common.process')]) }}
    <button type="button" id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200GeneralCancel"
            class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</button>
</div>
{{ Form::close() }}
