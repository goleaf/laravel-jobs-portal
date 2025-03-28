<div class="flex items-center">
    @if($row->image_url)
        <div class="mr-3 flex-shrink-0">
            <img src="{{ $row->image_url }}" alt="{{ $row->name }}" class="h-8 w-8 rounded-full object-cover shadow">
        </div>
    @endif
    <div>
        <a href="#" 
           class="font-medium text-gray-900 hover:text-blue-600 cursor-pointer" 
           x-data 
           @click="window.livewire.dispatch('showJobCategory', { id: {{ $row->id }} })">
            {{ $row->name }}
        </a>
    </div>
</div>
