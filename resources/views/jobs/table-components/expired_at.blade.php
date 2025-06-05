<div class="flex">
    @if(\Carbon\Carbon::now() > $row->job_expiry_date)
        <span class="badge bg-gray-100 danger">
            {{ \Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}
        </span>
    @else
        <span class="badge bg-gray-100 info">
            {{ \Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}
        </span>
    @endif
</div>
