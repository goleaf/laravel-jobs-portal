<div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-gray-100 info">
    <div>{{ Carbon\Carbon::parse($row->created_at)->translatedFormat('jS M, Y') }}</div>
</div>
