@if($row->roles->isNotEmpty())
    <span class="px-2 py-1 text-xs font-medium rounded-full 
        {{ $row->roles->first()->name === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
        {{ $row->roles->first()->name === 'candidate' ? 'bg-blue-100 text-blue-800' : '' }}
        {{ $row->roles->first()->name === 'employer' ? 'bg-green-100 text-green-800' : '' }}
    ">
        {{ ucfirst($row->roles->first()->name) }}
    </span>
@else
    <span class="text-gray-500">None</span>
@endif 