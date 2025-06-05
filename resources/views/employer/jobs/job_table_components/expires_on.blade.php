@if (\Carbon\Carbon::now() > $row->job_expiry_date)
    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 danger">
        <div>{{ Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}</div>
    </div>
@else
    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 info">
        <div>{{ Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}</div>
    </div>
@endif
