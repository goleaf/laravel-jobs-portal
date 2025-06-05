@if($row->is_featured_label === 'Yes')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium -light-success fs-7">{{ $row->is_featured_label }}</span>
@elseif($row->is_featured_label === 'No')
    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium -light-danger fs-7">{{ $row->is_featured_label }}</span>
@endif
