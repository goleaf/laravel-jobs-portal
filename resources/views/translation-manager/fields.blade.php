<div class="flex flex-wrap flex-1 -12">
    <div class="flex-1 -sm-3 mb-5">
        {{Form::select('translate_language', $allLanguagesArr,$selectedLang, ['class' => 'form-select translateLanguage','id'=>'translateLanguage','placeholder' => __('messages.company.select_language')]) }}
    </div>
    <div class="flex-1 -sm-3 mb-5">
        {{Form::select('file_name', $allFiles,$selectedFile, ['class' => 'form-select translate-language-files','placeholder' => 'Select File', 'id'=>'subFolderFiles']) }}
    </div>
    <div class="flex-1 -sm-3 mb-5 flex justify-end offset-3">
        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -primary addLanguageModal pt-3 me-2">{{ __('messages.common.add') }}</a>
        {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2','name' => 'save', 'id' => 'saveJob']) }}
    </div>
    <hr>
    <br>
    @foreach($languages as $key => $value)
        @if(!is_array($value))
            <div class="flex-1 -sm-2 mb-5">
                {{ Form::label('title', str_replace('_',' ',ucfirst($key)).':', ['class' => 'form-label']) }}
                {{ Form::text($key, $value, ['class' => 'form-control','required','placeholder' => str_replace('_',' ',ucfirst($key))]) }}
            </div>
        @else
            @foreach($value as $nestedKey => $nestedValue)
                @if(!is_array($nestedValue))
                    <div class="lg:w-2/12 px-2 flex-1 -md-3 mb-4">
                        {{ Form::label('title',  str_replace('_',' ',ucfirst($nestedKey)) .':', ['class' => 'form-label']) }}
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500" name="{{$key}}[{{$nestedKey}}]"
                               value="{{ $nestedValue }}" placeholder="{{str_replace('_',' ',ucfirst($nestedKey))}}"/>
                    </div>
                @endif
            @endforeach
        @endif
    @endforeach
</div>
