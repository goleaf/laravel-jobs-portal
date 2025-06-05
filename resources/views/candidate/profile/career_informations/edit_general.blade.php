{{ Form::open(['id'=>'editGeneralForm']) }}
<div class="flex flex-wrap">
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('first_name',__('messages.candidate.first_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('first_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','required','id'=> 'first_name','placeholder'=> __('messages.candidate.first_name')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('last_name',__('messages.candidate.last_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('last_name', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','required','id'=>'last_name','placeholder'=> __('messages.candidate.last_name')]) }}
    </div>
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('email',__('messages.candidate.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::text('email', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','required','id'=>'email','disabled']) }}
    </div>
    {{ --    <div class="form-group flex-1 sm-6 mb-5">-- }}
    {{ --        {{ Form::label('phone',__('messages.candidate.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 fs-6 fw-bolder text-gray-700 mb-3']) }}--}}
    {{ --        {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm-solid','id'=>'phone','placeholder'=>__('messages.candidate.phone')]) }}--}}
    {{ --    </div>-- }}
    <div class="flex-1 sm-6 mb-5">
        {{ Form::label('phone',__('messages.candidate.phone').(':'),['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex-1 sm-12 mb-5">
            {{ Form::tel('phone',  null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phone','placeholder'=>__('messages.candidate.phone')]) }}
        </div>
    </div>
    <div class="flex-1 sm-12 mb-5">
        {{ Form::label('skillId',__('messages.candidate.candidate_skill').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('candidateSkills[]',$data['skills'], (count($candidateSkills) > 0)?$candidateSkills:null, ['class' => 'form-select ','id'=>'skillId','multiple'=>true,'required']) }}
    </div>

    <div class="flex-1 sm-12 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1 ']) }}
        <span class="required"></span>
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'candidateCountryId','class' => 'form-select','placeholder' => __('messages.company.select_country'),'required']) }}
    </div>
    <div class="flex-1 sm-12 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', [],  null, ['id'=>'candidateStateId','class' => 'form-select ','placeholder' => __('messages.company.select_state')]) }}
    </div>
    <div class="flex-1 sm-12 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', [],  null, ['id'=>'candidateCityId','class' => 'form-select','placeholder' => __('messages.company.select_city')]) }}
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3','id'=>'btnEditGeneralSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span>".__('messages.common.process')]) }}
    <button type="button" id="btnGeneralCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors secondary me-2">{{ __('messages.common.cancel') }}</button>
</div>
{{ Form::close() }}
