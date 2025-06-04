<?php

    $isFeaturedEnable = App\Models\FrontSetting::where('key', 'featured_jobs_enable')->first();
    $isFeaturedEnable = ($isFeaturedEnable) ? $isFeaturedEnable->value : 0;
    $isFeaturedEnable = ($isFeaturedEnable == 1) ? true : false;
    $featured = $row->featured;
    $expiryDate = '';
    if ($featured) {
        $expiryDate = Carbon\Carbon::parse($featured->end_time)->translatedFormat('jS M, Y');
    }
    $totalFeaturedJob = App\Models\Job::Has('activeFeatured')->count();
    $maxFeaturedJob = App\Models\FrontSetting::where('key', 'featured_jobs_quota')->first()->value;
    $isFeaturedAvilabal = ($totalFeaturedJob >= $maxFeaturedJob) ? false : true;
    $isJobLive = $row->status ? true : false;
?>

@if($isFeaturedEnable)
    @if(!$featured)
        @if($isFeaturedAvilabal && $isJobLive)
            @if ($row->status == 3)
                <i class="font-20 fas fa-times-circle text-red-600"></i>
            @else
                <a title="{{__('messages.front_settings.pay_to_get')}} {{__('messages.front_settings.make_featured')}}"
                   data-bs-toggle="tooltip" data-bs-placement="bottom"
                   class="btn bg-blue-500 text-white hover:bg-blue-600 text-white px-3 py-1.5 text-sm action-btn w-full featured-job feature- px-4 py-2 rounded font-medium transition-colors"
                   data-id="{{$row->id}}">
                    {{__('messages.front_settings.make_featured')}}
                </a>
            @endif
        @endif
    @else
        @if ($row->status == 3)
            <i class="font-20 fas fa-times-circle text-red-600"></i>
        @else
            <a title="{{__('messages.front_settings.expires_on')}} {{$expiryDate}}
                    " data-bs-toggle="tooltip" class="btn bg-green-600 text-white hover:bg-green-700 text-white px-3 py-1.5 text-sm action- px-4 py-2 rounded font-medium transition-colors w-full"
               data-id="{{encrypt($row->id)}}">
                {{__('messages.front_settings.featured')}} <i class="far fa-check-circle pl-1"></i>
            </a>
        @endif
    @endif
@else
    <a class="btn btn-icon bg-red-600 text-white hover:bg-red-700 action- px-4 py-2 rounded font-medium transition-colors"><i class="fas fa-times"></i></a>
@endif


{{--@php--}}
{{--    $todayDate = Carbon\Carbon::now()->format('Do MMM, YYYY');--}}
{{--    $expiredDate = Carbon\Carbon::parse($row->job_expiry_date)->isoFormat('Do MMM, YYYY');--}}
{{--@endphp--}}
{{--@if ($todayDate > $expiredDate)--}}
{{--    <div class="badge badge-light-primary">--}}
{{--        <i class="font-20 fas fa-times-circle text-red-600"></i>--}}
{{--    </div>--}}
{{--@endif--}}
