<div class="flex justify-center">
    @if (!$row->featured)
        <div class="dropdown">
            <a class="btn bg-gray-500 text-white hover:bg-gray-600 text-white px-4 py-2 rounded font-medium transition-colors -sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.make_feature'); ?>
            </a>
            <ul class="fs-6 py-4 dropdown-menu customDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="btn px-4 py-2 rounded font-medium transition-colors -sm adminMakeFeatured" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.make_featured'); ?></a>
                </li>
            </ul>
        </div>
    @else
        <div title="{{ __('messages.front_settings.expires_on') }} {{ Carbon\Carbon::parse($row->featured->end_time)->translatedFormat('jS M, Y') }}"data-bs-toggle="tooltip"
            class="dropdown">
            <a class="btn bg-green-600 text-white hover:bg-green-700 px-4 py-2 rounded font-medium transition-colors -sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.featured'); ?>
                <i class="far fa-check-circle pl-1"></i>
            </a>
            <ul class="fs-6 py-4 dropdown-menu featuredDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="btn px-4 py-2 rounded font-medium transition-colors -sm adminUnFeatured" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.remove_featured'); ?></a>
                </li>
            </ul>
    @endif
</div>
</div>
