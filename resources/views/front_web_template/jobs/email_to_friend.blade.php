<div class="fixed inset-0 z-50 overflow-y-auto fade" id="emailJobToFriendModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded -lg shadow-xl max-w-lg w-full">
            <div class="px-6 py-4 border-b border-gray-200 border border border-gray-300 -gray-300 -bottom-0">
                <h5 class="fixed inset-0 z-50 overflow-y-auto-title" id="exampleModalLongTitle">{{ __('messages.job.email_to_friend') }}</h5>
                <button type="button" class="px-4 py-2 rounded font-medium transition-colors close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="frm" id="emailJobToFriend">
                <div class="px-6 py-4">
                    @csrf
                    <input type="hidden" name="user_id"
                           value="{{ (getLoggedInUserId() !== null) ? getLoggedInUserId() : null }}">
                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                    <div class="mb-4 mb-md-4 mb-3">
                        <label for="" class="fs-16 text-gray-600 mb-3" for="jobUrl">{{ __('messages.job.job_url') }}</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3"
                        name="job_url" id="jobUrl" readonly>
                    </div>

                    <div class="mb-4 mb-md-4 mb-3">
                        <label class="fs-16 text-gray-600 mb-2" for="friendName">{{ __('messages.job.friend_name') }}</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" name="friend_name" id="friendName" required>
                    </div>

                    <div class="mb-4 mb-md-4 mb-3">
                        <label class="fs-16 text-gray-600 mb-2" for="friendEmail">{{ __('messages.job.friend_email') }}</label>
                        <input type="email" class="w-full px-3 py-2 border border-gray-300 border border border-gray-300 -gray-300 -gray-300 rounded -md focus:outline-none focus:ring-2 focus:ring-primary-500 fs-14 text-gray bg-white br-10 p-3" name="friend_email" id="friendEmail" required>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-2 border border border-gray-300 -gray-300 -top-0">
                    <button type="button" class="border border-gray-300 bg-transparent"
                            data-bs-dismiss="modal">{{ __('messages.common.close') }}</button>
                    <button type="submit" class="border border-gray-300 bg-transparent"
                            data-bs-loading-text="<span class="animate-spin h-5 w-5 border-2 border-current border-t-transparent rounded -full spinner- border border border-gray-300 -gray-300 -sm"></span> {{ __('messages.common.process') }}"
                            id="btnSendToFriend">{{ __('web.job_details.send_to_friend') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
