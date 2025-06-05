<div class="flex justify-center">
    @if( !$flex flex-wrap -mx-4->is_trial_plan == 1 )
        <i class="fas fa-times-circle text-red-600 h3"></i>
    @else
        <i class="fas fa-check-circle text-green-600 h3"></i>
    @endif
</div>
