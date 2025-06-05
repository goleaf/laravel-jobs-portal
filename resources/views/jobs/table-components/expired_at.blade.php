<div class="flex">
    @if(\Carbon\Carbon::now() > $row->job_expiry_date)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 danger">
            {{ \Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}
        </span>
    @else
        <span class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 info">
            {{ \Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}
        </span>
    @endif
</div>
