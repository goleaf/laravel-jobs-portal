<div class="flex justify-center">
    @if (!$row->featured)
        <div class="relative inline-block text-left">
            <a class="rounded-md transition" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.make_feature'); ?>
            </a>
            <ul class="fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 customDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="rounded-md transition" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.make_featured'); ?></a>
                </li>
            </ul>
        </div>
    @else
        <div title="{{ __('messages.front_settings.expires_on') }} {{ Carbon\Carbon::parse($row->featured->end_time)->translatedFormat('jS M, Y') }}"data-bs-toggle="tooltip"
            class="relative inline-block text-left">
            <a class="rounded-md transition" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo __('messages.front_settings.featured'); ?>
                <i class="far fa-check-circle pl-1"></i>
            </a>
            <ul class="fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 featuredDropdown" aria-labelledby="dropdownMenuButton1">
                <li><a class="rounded-md transition" data-id="{{ $row->id }}"><?php echo __('messages.front_settings.remove_featured'); ?></a>
                </li>
            </ul>
    @endif
</div>
</div>
