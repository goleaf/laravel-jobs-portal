<div class="flex items-center">
    <div class="image image-circle image-mini me-3">
        <img src="{{ $row->$job->$company->company_url }}" alt=""
             class="">
    </div>
    <div class="flex flex-col">
        <a href="{{ route('front.', $row->$job->job_id) }}" class="text-decoration-none"
           target="_blank">{{ $row->$job->job_title }}</a>
    </div>
</div>
