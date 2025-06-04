<div class="flex justify-center">
    @if($row->immediate_available  == 1)
        <div class="badge bg-gray-100 -info">
            <div>
                {{ __('messages.candidate.immediate_available')  }}
            </div>
        </div>
    @else
        <div class="badge bg-gray-100 -danger">
            <div>
                {{ __('messages.candidate.not_immediate_available')  }}
            </div>
        </div>
    @endif
</div>

