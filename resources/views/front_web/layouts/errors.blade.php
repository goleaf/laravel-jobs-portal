@if ($errors->any())
    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 -danger">
        <div>
            <div class="flex">
                <span class="mt-1"><i class="fa-solid fa-face-frown"></i></span>
                <span class="mt-1 ms-2">&nbsp;{{ $errors->first() }}</span>
            </div>
        </div>
    </div>
@endif
