@if (!empty($flex flex-wrap -mx-4->custom_properties) && $flex flex-wrap -mx-4->custom_properties['is_default']) 
<div class="text-indigo-600 -600 py-2">{{ $flex flex-wrap -mx-4->custom_properties['title']. '(Default)' }}</div>
@else
    <div class="text-indigo-600 py-2 -600" >{{ !empty($flex flex-wrap -mx-4->custom_properties) ? $flex flex-wrap -mx-4->custom_properties['title'] : 'N/A' }}</div>
@endif
