<div class="flex justify-center">
    @if (!$row->featured)
        <div class="relative inline-block text-left">
            <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-gray-500 text-white hover:bg-gray-600 text-white px-4 py-2 rounded font-medium transition-colors -sm inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.make_feature'); ?>
            </a>
            <ul class="fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 customDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm adminMakeFeatured" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.make_featured'); ?></a>
                </li>
            </ul>
        </div>
    @else
        <div title="{{ __('messages.front_settings.expires_on') }} {{ Carbon\Carbon::parse($row->featured->end_time)->translatedFormat('jS M, Y') }}"data-bs-toggle="tooltip"
            class="relative inline-block text-left">
            <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded font-medium transition-colors -sm inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.featured'); ?>
                <i class="far fa-check-circle pl-1"></i>
            </a>
            <ul class="fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 featuredDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm adminUnFeatured" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.remove_featured'); ?></a>
                </li>
            </ul>
    @endif
</div>
</div>
