@formOpen(['id' => 'editOnlineProfileForm'])
<div class="flex-wrap flex">
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label(__('messages.company.facebook_url'), __('messages.company.facebook_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-facebook-f facebook-fa-icon"></i>
            </div>
            {{ Form::text('facebook_url',$user->facebook_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'facebookId','placeholder'=>'https://www.facebook.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label(__('messages.company.twitter_url'), __('messages.company.twitter_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-twitter twitter-fa-icon"></i>
            </div>
            {{ Form::text('twitter_url', $user->twitter_url , ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm ','id'=>'twitterId','placeholder'=>'https://www.twitter.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label(__('messages.company.linkedin_url'), __('messages.company.linkedin_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-linkedin-in linkedin-fa-icon"></i>
            </div>
            {{ Form::text('linkedin_url', $user->linkedin_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'linkedinId','placeholder'=>'https://www.linkedin.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label(__('messages.company.google_plus_url'), __('messages.company.google_plus_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-google-plus-g google-plus-fa-icon"></i>
            </div>
            {{ Form::text('google_plus_url', $user->google_plus_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'googlePlusId','placeholder'=>'https://www.plus.google.com']) }}
        </div>
    </div>
    <div class="mb-5 flex-1 px-4 -xl-6 md:w-6/12 flex-1 sm-12">
        {{ Form::label(__('messages.company.pinterest_url'), __('messages.company.pinterest_url').':', ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
        <div class="flex">
            <div class="rounded border border border border border border border-gray-300 -gray-300 px-3 py-2 bg-gray-50 -gray-300 -gray-300 -r-0 -l-md text-gray-500 -0">
                <i class="fab fa-pinterest-p pinterest-fa-icon"></i>
            </div>
            {{ Form::text('pinterest_url', $user->pinterest_url, ['class' => 'block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm','id'=>'pinterestId','placeholder'=>'https://www.pinterest.com']) }}
        </div>
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'rounded-md bg-indigo-600 px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200OnlineProfileSave','data-loading-text'=>"<span class="rounded border border border border border border-gray-300 -gray-300 animate-spin -full -2 -gray-300 -t-blue-600 spinner- -sm"></span> Processing..."]) }}
    <button type="button" id="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-flex-1 px-4ors duration-200OnlineProfileCancel"
            class="border border-gray-300 bg-transparent">{{ __('messages.common.cancel') }}</button>
</div>
@formClose()
