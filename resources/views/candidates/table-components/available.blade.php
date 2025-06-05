<div class="flex justify-center">
    @if($row->immediate_available == 1)
        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
            <div>
                {{ __('messages.candidate.immediate_available') }}
            </div>
        </div>
    @else
        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
            <div>
                {{ __('messages.candidate.not_immediate_available') }}
            </div>
        </div>
    @endif
</div>

