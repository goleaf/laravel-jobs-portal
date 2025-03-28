<div class="flex justify-center">
    @if(!$row->featured)
        <div class="relative inline-block text-left">
            <button type="button" 
                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                    id="featured-menu-button-{{ $row->id }}"
                    aria-expanded="false"
                    aria-haspopup="true"
                    onclick="toggleFeaturedMenu({{ $row->id }})">
                {{ __('messages.front_settings.make_feature') }}
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                 id="featured-menu-{{ $row->id }}"
                 role="menu" 
                 aria-orientation="vertical" 
                 aria-labelledby="featured-menu-button-{{ $row->id }}"
                 tabindex="-1">
                <div class="py-1" role="none">
                    <button type="button"
                            class="adminJobMakeFeatured text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                            data-id="{{ $row->id }}"
                            role="menuitem"
                            tabindex="-1">
                        {{ __('messages.front_settings.make_featured') }}
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="relative inline-block text-left" 
             title="Expires On {{ Carbon\Carbon::parse($row->featured->end_time)->translatedFormat('jS M, Y') }}">
            <button type="button" 
                    class="inline-flex justify-center w-full px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    id="featured-menu-button-{{ $row->id }}"
                    aria-expanded="false"
                    aria-haspopup="true"
                    onclick="toggleFeaturedMenu({{ $row->id }})">
                {{ __('messages.front_settings.featured') }}
                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </button>
            <div class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none hidden"
                 id="featured-menu-{{ $row->id }}"
                 role="menu" 
                 aria-orientation="vertical" 
                 aria-labelledby="featured-menu-button-{{ $row->id }}"
                 tabindex="-1">
                <div class="py-1" role="none">
                    <button type="button"
                            class="adminJobUnFeatured text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                            data-id="{{ $row->id }}"
                            role="menuitem"
                            tabindex="-1">
                        {{ __('messages.front_settings.remove_featured') }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleFeaturedMenu(id) {
    const menu = document.getElementById(`featured-menu-${id}`);
    menu.classList.toggle('hidden');
}
</script>
