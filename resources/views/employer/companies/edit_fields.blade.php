<div class="flex flex-wrap">
    {{ Form::hidden('user_id',$user->id) }}
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('name', __('messages.company.name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('name', isset($user)?$user->full_name:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.company.name')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('email', __('messages.company.email').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::email('email', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'placeholder' => __('messages.company.email')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mobile-itel-width mb-5">
        {{ Form::label('phone', __('messages.user.phone').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::tel('phone', null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")','id'=>'phoneNumber']) }}
        {{ Form::hidden('region_code',null,['id'=>'prefix_code']) }}
        <span id="valid-msg" class="hidden text-green-600 block fw-400 fs-small mt-2">{{ __('messages.phone.valid_number') }}</span>
        <span id="error-msg" class="hidden text-red-600 block fw-400 fs-small mt-2"></span>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('ceo', __('messages.company.ceo_name').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('ceo', (isset($company) ? $company->ceo: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','text-red-500', 'placeholder' => __('messages.company.ceo_name')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('industry_id', __('messages.company.industry').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('industry_id', $data['industries'],isset($company)?$company->industry_id:null, ['id'=>'industryId','data-control'=>'select2','class' => 'form-select','placeholder' => __('messages.company.select_industry'),'text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('ownership_type_id', __('messages.company.ownership_type').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('ownership_type_id', $data['ownerShipTypes'], isset($company)?$company->ownership_type_id:null, ['id'=>'ownershipTypeId','class' => 'form-select','placeholder' => __('messages.company.select_ownership_type'),'data-control'=>'select2','text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('company_size_id', __('messages.company.company_size').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::select('company_size_id', $data['companySize'], isset($company)?$company->company_size_id:null, ['id'=>'companySizeId','class' => 'form-select','placeholder' => __('messages.company.select_company_size'),'data-control'=>'select2','text-red-500']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('country', __('messages.company.country').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('country_id', $data['countries'], null, ['id'=>'countryId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_country')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('state', __('messages.company.state').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('state_id', (isset($states) && $states!=null?$states:[]), null, ['id'=>'stateId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_state')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('city', __('messages.company.city').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::select('city_id', (isset($cities) && $cities!=null?$cities:[]), null, ['id'=>'cityId','class' => 'form-select','data-control'=>'select2','placeholder' => __('messages.company.select_city')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('established_in', __('messages.company.established_in').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::selectYear('established_in', date('Y'), 2000, (isset($company->established_in)) ? $company->established_in : '', ['class' => 'form-select','data-control'=>'select2', 'id' => 'establishedIn']) }}
    </div>
    <div class="flex-1 -xl-12 md:w-full flex-1 sm-12 mb-5">
        {{ Form::label('details', __('messages.company.employer_details').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        <div id="editEmployeeDetails"></div>
        {{ Form::hidden('details', $company->details, ['id' => 'editEmployerDetail']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('location', __('messages.company.location').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('location', (isset($company) ? $company->location: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.company.location')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('location2', __('messages.company.location2').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::text('location2', (isset($company) ? $company->location2: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.company.location2')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('no_of_offices', __('messages.company.no_of_offices').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <span class="required"></span>
        {{ Form::text('no_of_offices', (isset($company) ? $company->no_of_offices: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'text-red-500', 'placeholder' => __('messages.company.no_of_offices') , 'onkeyup' => 'if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,"")']) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('website', __('messages.company.website').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::text('website', (isset($company) ? $company->website: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.company.website')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('fax', __('messages.company.fax').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        {{ Form::text('fax',(isset($company) ? $company->fax: null), ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm', 'placeholder' => __('messages.company.fax')]) }}
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('facebook_url', __('messages.company.facebook_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border-gray-300 -0">
                <i class="fab fa-facebook-f facebook-fa-icon text-indigo-600 -600"></i>
            </div>
            {{ Form::text('facebook_url', isset($company->$user->facebook_url)?$company->$user->facebook_url:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'facebookUrl','placeholder'=>'https://www.facebook.com']) }}
        </div>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('twitter_url', __('messages.company.twitter_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border-gray-300 -0">
                <i class="fab fa-twitter twitter-fa-icon text-indigo-600 -600"></i>
            </div>
            {{ Form::text('twitter_url', isset($company->$user->twitter_url)?$company->$user->twitter_url:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'twitterUrl','placeholder'=>'https://www.twitter.com']) }}
        </div>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('linkedin_url', __('messages.company.linkedin_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border-gray-300 -0">
                <i class="fab fa-linkedin-in linkedin-fa-icon text-indigo-600 -600"></i>
            </div>
            {{ Form::text('linkedin_url', isset($company->$user->linkedin_url)?$company->$user->linkedin_url:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'linkedInUrl','placeholder'=>'https://www.linkedin.com']) }}
        </div>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('google_plus_url', __('messages.company.google_plus_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border-gray-300 -0">
                <i class="fab fa-google-plus-g google-plus-fa-icon text-red-600"></i>
            </div>
            {{ Form::text('google_plus_url', isset($company->$user->google_plus_url)?$company->$user->google_plus_url:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'googlePlusUrl','placeholder'=>'https://www.plus.google.com']) }}
        </div>
    </div>
    <div class="flex-1 -xl-6 md:w-6/12 flex-1 sm-12 mb-5">
        {{ Form::label('pinterest_url', __('messages.company.pinterest_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded -l-md text-gray-500 border border-gray-300 -0">
                <i class="fab fa-pinterest-p pinterest-fa-icon text-red-600"></i>
            </div>
            {{ Form::text('pinterest_url', isset($company->$user->pinterest_url)?$company->$user->pinterest_url:null, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'pinterestUrl','placeholder'=>'https://www.pinterest.com']) }}
        </div>
    </div>

    <!-- Submit Field -->
    <div class="flex justify-end">
        {{ Form::submit(__('messages.common.save'), ['class' => 'rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none transition-colors me-3']) }}
        <a href="{{ route('company.edit', \Illuminate\Support\Facades\Auth::user()->owner_id) }}"
           class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</a>
    </div>

</div>
