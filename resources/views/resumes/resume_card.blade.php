<div class="flex-1 -xl-4 flex-1 md-6 candidate- bg-white shadow rounded -lg overflow-hidden">
    <div class="hover-effect-employee relative mb-5 border-hover-primary employee- border border-gray-300">
        <div class="employee-listing-details">
            <div class="flex employee-listing-description items-center justify-center flex- flex-1">
                <div class="pl-0 mb-2 employee-avatar">
                    <img src="{{ $candidateResume->$candidate->candidate_url }}"
                         class="img-responsive users-avatar-img employee-img mr-2">
                </div>
                <div class="mb-auto w-full employee-data">
                    <div class="flex justify-center items-center w-full">
                        <div>
                            <span class="text-decoration-none text-color-gray one-line-ellip">{{ $candidateResume->$candidate->$user->full_name }}</span>
                        </div>
                    </div>
                    <div class="text-center one-line-ellip">
                        <label class="employee-label">{{ __('messages.faq.title') }} :</label>
                        <span class="text-decoration-none text-color-gray">{{ $candidateResume->custom_properties['title'] }}</span>
                    </div>
                </div>
            </div>
            <div class="download-resume">
                <a href="{{ route('admin.') .'/'. $candidateResume->id }}"
                   class="download-link"><i class="fas fa-download"></i> {{ __('messages.common.download') }}</a>
            </div>
        </div>
    </div>
</div>
