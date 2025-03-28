<div>
    @if($row->is_active)
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
            Active
        </span>
    @else
        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
            Inactive
        </span>
    @endif
</div> 