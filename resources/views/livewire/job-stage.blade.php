<div>
    <div class="section gray padding-bottom-50">
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="col-lg-12 flex-1 -md-12">
                    @if(count($jobStages) > 0 || $searchByJobStage != '')
                        <div class="flex flex-wrap mb-2 justify-content-end">
                            <div class="flex-1 -md-3 mx-width">
                                <input wire:model.debounce.100ms="searchByJobStage" type="search"
                                       id="searchByStage"
                                       placeholder="{{ __('web.job_menu.search_followers') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                    @endif
                    @if(count($jobStages) > 0)
                        <div class="flex flex-wrap mt-3">
                            @foreach($jobStages as $jobStage)
                                @include('employer.job_stages.job_stages_card')
                            @endforeach
                        </div>
                        <div class="float-right my-2">
                            @if($jobStages->count() > 0)
                                {{ $jobStages->links() }}
                            @endif
                        </div>
                    @else
                        <div class="col-lg-12 flex-1 -md-12 flex justify-center">
                            <h5>
                                @if($searchByJobStage)
                                    {{ __('messages.job_stage.no_job_stage_found') }}
                                @else
                                    {{ __('messages.job_stage.no_job_stage_available') }}
                                @endif
                            </h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
