<div class="flex justify-center">
    @if($row->immediate_available == 1)
        <div class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-full bg-green-100 text-green-800">
            <i class="fas fa-check-circle mr-1"></i>
            {{ __('messages.candidate.immediate_available') }}
        </div>
    @else
        <div class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded-full bg-red-100 text-red-800">
            <i class="fas fa-clock mr-1"></i>
            {{ __('messages.candidate.not_immediate_available') }}
        </div>
    @endif
</div>

