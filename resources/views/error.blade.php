@if ($errors->any())
    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger p-0">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
