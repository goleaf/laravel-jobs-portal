@foreach($data['candidateExperiences'] as $candidateExperience)
    <div class="shadow rounded p-5 mb-5 w-full px-4-sm-12 md:w-full flex-1 lg-12 candidate-experience"
         data-experience-id="{{ $loop->index }}"
         data-id="{{ $candidateExperience->id }}">
        <article class="article article-style-b">
            <div class="border article-details -0">
                <div class="flex justify-between">
                    <div class="article-title">
                        <h5 class="text-indigo-600 experience-title -600">{{ Str::limit($candidateExperience->experience_title,50,'...') }}</h5>
                        <h6 class="text-gray-500">{{ $candidateExperience->company }}</h6>
                    </div>
                    <div class="article-cta candidate-experience-edit-delete">
                        <a href="javascript:void(0)"
                           class="transition duration-150 ease-in-out flex-1"
                           title="{{ __('messages.common.edit') }}" data-bs-toggle="tooltip"
                           data-id="{{ $candidateExperience->id }}"> <i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:void(0)"
                           class="transition duration-150 ease-in-out flex-1"
                           title="{{ __('messages.common.delete') }}" data-bs-toggle="tooltip"
                           data-id="{{ $candidateExperience->id }}"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
                <span class="text-gray-500">{{ \Carbon\Carbon::parse($candidateExperience->start_date)->translatedFormat('jS M, Y') }} - </span>
                <span class="text-gray-500">
                    {{ ($candidateExperience->currently_working) ? __('messages.candidate_profile.present') : \Carbon\Carbon::parse($candidateExperience->end_date)->translatedFormat('jS M, Y') }}
                 | {{ $candidateExperience->country }}</span>
                @if(!empty($candidateExperience->description))
                    <p class="mb-0">{{ Str::limit($candidateExperience->description,225,'...') }}</p>
                @endif
            </div>
        </article>
    </div>
@endforeach
