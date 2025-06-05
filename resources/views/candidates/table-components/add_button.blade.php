<div class="menu-item">
    <a href="{{ route('admin.candidates.create') }}" type="button" class="border border-gray-300 bg-transparent">
        {{ __('messages.common.add') }}
    </a>
</div>
@if(Auth::user()->hasRole('Admin'))
    <div class="menu-item px-2">
        <a href="{{ route('admin.candidates.index') }}" type="button" class="border border-gray-300 bg-transparent" data-turbo="false">
            {{ __('messages.common.export_excel') }}
        </a>
    </div>
    @endif

