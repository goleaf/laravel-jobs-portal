<span class="text-gray-600">
    {{ \Carbon\Carbon::parse($row->created_at)->format('d M, Y') }}
</span>
