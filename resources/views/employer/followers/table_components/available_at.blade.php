<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 -{{ $ flex flex-wrap ->$user->$candidate->immediate_available == 1 ?"info' : 'danger' }} fs-6">{{ __('messages.candidate.immediate_available') }}</span>

