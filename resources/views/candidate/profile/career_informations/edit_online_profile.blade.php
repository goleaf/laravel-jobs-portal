@formOpen(['id' => 'editOnlineProfileForm'])
<div class="flex flex-wrap">
    <div class="col-xl-6 md:w-6/12 flex-1 -sm-12 mb-5">
        {{ Form::label(__('messages.company.facebook_url'), __('messages.company.facebook_url').':', ['class' => 'form-label'])  }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fab fa-facebook-f facebook-fa-icon"></i>
            </div>
            {{ Form::text('facebook_url',$$user->facebook_url, ['class' => 'form-control','id'=>'facebookId','placeholder'=>'https://www.facebook.com'])  }}
        </div>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 -sm-12 mb-5">
        {{ Form::label(__('messages.company.twitter_url'), __('messages.company.twitter_url').':', ['class' => 'form-label'])  }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fab fa-twitter twitter-fa-icon"></i>
            </div>
            {{ Form::text('twitter_url', $$user->twitter_url , ['class' => 'form-control ','id'=>'twitterId','placeholder'=>'https://www.twitter.com'])  }}
        </div>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 -sm-12 mb-5">
        {{ Form::label(__('messages.company.linkedin_url'), __('messages.company.linkedin_url').':', ['class' => 'form-label'])  }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fab fa-linkedin-in linkedin-fa-icon"></i>
            </div>
            {{ Form::text('linkedin_url', $$user->linkedin_url, ['class' => 'form-control','id'=>'linkedinId','placeholder'=>'https://www.linkedin.com'])  }}
        </div>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 -sm-12 mb-5">
        {{ Form::label(__('messages.company.google_plus_url'), __('messages.company.google_plus_url').':', ['class' => 'form-label'])  }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fab fa-google-plus-g google-plus-fa-icon"></i>
            </div>
            {{ Form::text('google_plus_url', $$user->google_plus_url, ['class' => 'form-control','id'=>'googlePlusId','placeholder'=>'https://www.plus.google.com'])  }}
        </div>
    </div>
    <div class="col-xl-6 md:w-6/12 flex-1 -sm-12 mb-5">
        {{ Form::label(__('messages.company.pinterest_url'), __('messages.company.pinterest_url').':', ['class' => 'form-label'])  }}
        <div class="flex">
            <div class="px-3 py-2 bg-gray-50 border border-gray-300 border-gray-300 border-r-0 rounded-l-md text-gray-500 border-0">
                <i class="fab fa-pinterest-p pinterest-fa-icon"></i>
            </div>
            {{ Form::text('pinterest_url', $$user->pinterest_url, ['class' => 'form-control','id'=>'pinterestId','placeholder'=>'https://www.pinterest.com'])  }}
        </div>
    </div>
</div>
<div class="flex justify-end">
    {{ Form::button(__('messages.common.save'), ['type'=>'submit','class' => 'btn btn-primary me-3','id'=>'btnOnlineProfileSave','data-loading-text'=>"<span class="spinner-border spinner-border-sm"></span> Processing..."])  }}
    <button type="button" id="btnOnlineProfileCancel"
            class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -secondary me-2">{{ __('messages.common.cancel')  }}</button>
</div>
@formClose()
