<div class="menu-item">
    <a href="{{ route('admin.candidates.create') }}" type="button" class="btn px-4 py-2 rounded font-medium transition-colors -primary">
        {{ __('messages.common.add') }}
    </a>
</div>
@if(Auth::user()->hasRole('Admin'))
    <div class="menu-item px-2">
        <a href="{{ route('candidates.export.excel') }}" type="button" class="btn px-4 py-2 rounded font-medium transition-colors -primary" data-turbo="false">
            {{ __('messages.common.export_excel') }}
        </a>
    </div>
    @endif

