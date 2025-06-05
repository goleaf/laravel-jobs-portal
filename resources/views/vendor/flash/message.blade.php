@foreach (session('flash_notification', collect())->toArray() as $message)
    @if ($message['overlay'])
        @include('flash::modal', [
            'modalClass' => 'flash-modal',
            'title'      => $message['title'],
            'body'       => $message['message']
        ])
    @else
        <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -{{ $message["level'] }} {{ $message['important'] ? 'alert-important' : '' }} custom-message bg-{{ $message['level'] }} border border-{{ $message['level'] }}">
            <div class="flex text-white items-center">
                <i class="fa-solid  fa-face-smile me-4"></i>
                <div>
                    <span class="text-white">{{ $message['message'] }}</span>
                </div>
            </div>
        </div>

    @endif
@endforeach

{{ session()->forget('flash_notification') }}
