@if (\Carbon\Carbon::now() > $row->job_expiry_date)
    <div class="badge bg-gray-100 danger">
        <div>{{ Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}</div>
    </div>
@else
    <div class="badge bg-gray-100 info">
        <div>{{ Carbon\Carbon::parse($row->job_expiry_date)->translatedFormat('jS M, Y') }}</div>
    </div>
@endif
