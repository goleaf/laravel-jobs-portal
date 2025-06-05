{{ --@if ($errors->any())-- }}
{{ --    <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger">-- }}
{{ --        <ul class="j-error-padding list-unstyled p-2 mb-0">-- }}
{{ --            <li class="text-white">{{ $errors->first() }}</li>--}}
{{ --        </ul>-- }}
{{ --    </div>-- }}
{{ --@endif-- }}

@if(!empty($errors))
    @if ($errors->any())
        <div class="px-4 py-3 rounded-md border border-gray-300 mb-4 p-4 rounded-md mb-4 danger">
            <div>
                <div class="flex">
                    <span class="mt-1"><i class="fa-solid fa-face-frown me-1"></i></span>
                    <span class="mt-1">&nbsp;{{ $errors->first() }}</span>
                </div>
            </div>
        </div>
    @endif
@endif
