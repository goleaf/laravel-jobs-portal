@if (!empty($row->custom_properties) && $row->custom_properties['is_default']) 
<div class="text-primary-600 py-2">{{ $row->custom_properties['title']. '(Default)' }}</div>
@else
    <div class="py-2 text-primary-600" >{{ !empty($row->custom_properties) ? $row->custom_properties['title'] : 'N/A' }}</div>
@endif
