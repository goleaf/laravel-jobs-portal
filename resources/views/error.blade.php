@if ($errors->any())
    <div class="alert p-4 rounded-md mb-4 -danger p-0">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
