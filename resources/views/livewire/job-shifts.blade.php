<div class="employee- bg-white shadow rounded-lg overflow-hidden">
    <div class="flex flex-wrap">
        @if(count($jobShifts) > 0 || $searchByJobShifts != '')
            <div class="flex-1 -md-12">
                <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                    <div>
                        <div class="selectgroup mr-4">
                            <input wire:model.debounce.100ms="searchByJobShifts" id="searchByJobShifts"
                                   type="search"
                                   autocomplete="off"
                                   placeholder="{{ __('web.common.search')  }}" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse($jobShifts as $jobShift)
            @include('job_shifts.job_shifts_card')
        @empty
            <div class="flex-1 -md-12">
                <h5 class="text-black text-center">
                    @if ($searchByJobShifts)
                        {{ __('messages.job_shift.no_job_shifts_found')  }}
                    @else
                        {{ __('messages.job_shift.no_job_shifts_available')  }}
                    @endif
                </h5>
            </div>
        @endforelse
        <div class="flex-1 -md-12">
            <div class="flex flex-wrap mb-3 justify-end flex-wrap">
                @if($jobShifts->count() > 0)
                    {{ $jobShifts->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

