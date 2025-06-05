@if (Carbon\Carbon::now() > $flex flex-wrap -mx-4->$job->job_expiry_date)
    <div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-gray-100 danger">
        <div>{{ Carbon\Carbon::parse($flex flex-wrap -mx-4->$job->job_expiry_date)->translatedFormat('jS M Y') }}</div>
    </div>
@else
    <div class="rounded inline-flex items-center px-2.5 py-0.5 -full text-xs font-medium bg-gray-100 info">
        <div>{{ Carbon\Carbon::parse($flex flex-wrap -mx-4->$job->job_expiry_date)->translatedFormat('jS M Y') }}</div>
    </div>
@endif

