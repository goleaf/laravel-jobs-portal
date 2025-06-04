<div class="flex flex-wrap">
    @forelse($jobs as $job)
        <div class="flex-1 -lg-12 px-lg-3">
            <div class="job- bg-white shadow rounded-lg overflow-hidden">

                <div class=" mb-40">
                    <a href="{{ route('front.job.details', $job['job_id']) }}" class="bg-white shadow rounded-lg overflow-hidden py-30 border-0">
                        <div class="d-sm-flex relative">
                            <div class="mb-sm-0 mb-3 me-sm-4">
                                <img src="{{ $job->company->company_url }}" class="bg-white shadow rounded-lg overflow-hidden -img" alt="...">
                            </div>
                            <div class="">
                                <div class="bg-white shadow rounded-lg overflow-hidden -body p-0">
                                    <h5 class="bg-white shadow rounded-lg overflow-hidden -title text-gray-600 fs-18 mb-0">
                                        {{ html_entity_decode(Str::limit($job['job_title'], 50)) }}
                                        @if (isset($job->jobShift->shift))
                                        <span class="text text-primary-600 fs-6 mb-0 me-3">
                                            {{ $job->jobShift->shift }}
                                        </span>
                                        @endif
                                    </h5>
                                    <div class="">
                                        <div class="bg-white shadow rounded-lg overflow-hidden -desc flex flex-wrap mt-2">
                                            <div class="desc flex me-4">
                                              <div class="me-3 w-20">
                                                <x-icons.briefcase class="w-full" />
                                              </div>
                                              <p class="fs-14 text-gray mb-2">{{ $job->jobCategory->name }}</p>
                                            </div>
                                            <div class="desc flex me-4">
                                              <div class="me-3 w-20">
                                                <x-icons.location class="w-full" />
                                              </div>
                                              <p class="fs-14 text-gray mb-2">{{ !empty($job->full_location) ? $job->full_location : 'Location Info. not available.' }}</p>
                                            </div>
                                            <div class="desc flex">
                                              <div class="me-3 w-20">
                                                <x-icons.clock class="w-full" />
                                              </div>
                                              <p class="fs-14 text-gray mb-2">{{ $job->created_at->diffForHumans() }}</p>
                                            </div>
                                          </div>
                                    </div>
                                    <div class="desc flex">
                                        <p class="text text-primary-600 fs-14 mb-0 me-3">
                                            {{ !empty($job->jobsSkill[0]->name) ? $job->jobsSkill[0]->name : 'Skill' }}
                                        </p>
                                        <p class="fs-14 text text-primary-600 mb-0">{{ $job->jobsSkill->count() }}+</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    @empty
        <div class="flex-1 -md-12 text-center text-gray">
            @lang('web.job_menu.no_results_found')
        </div>
    @endforelse
    @if($jobs->count() > 0)
        {{$jobs->links() }}
    @endif
</div>
