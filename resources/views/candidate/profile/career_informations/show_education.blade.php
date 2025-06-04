@foreach($data['candidateEducations'] as $candidateEducation)
    <div class="w-full col-sm-12 md:w-full flex-1 -lg-12 candidate-education shadow rounded p-5 mb-5"
         data-education-id="{{ $loop->index  }}"
         data-id="{{ $candidateEducation->id  }}">
        <article class="article article-style-b">
            <div class="article-details border-0">
                <div class="flex justify-between">
                    <div class="article-title">
                        <h5 class="education-degree-level text-primary-600">{{ $candidateEducation->degreeLevel->name  }}</h5>
                        <h6 class="text-gray-500">{{ $candidateEducation->degree_title  }}</h6>
                    </div>
                    <div class="article-cta candidate-education-edit-delete">
                        <a href="javascript:void(0)"
                           class="px-4 py-2 rounded font-medium transition-colors px-2 text-primary-600 fs-3 ps-0 edit-education"
                           title="{{ __('messages.common.edit') }}"
                           data-id="{{ $candidateEducation->id  }}" data-bs-toggle="tooltip">
                            <i class="fa-solid fa-pen-to-square"></i></a>
                        <a href="javascript:void(0)"
                           class="px-4 py-2 rounded font-medium transition-colors px-2 text-red-600 fs-3 pe-0 delete-education"
                           title="{{ __('messages.common.delete') }}" data-bs-toggle="tooltip"
                           data-id="{{ $candidateEducation->id  }}"><i class="fa-solid fa-trash"></i></a>
                    </div>
                </div>
                <span class="text-gray-500">{{ $candidateEducation->year  }} | {{ $candidateEducation->country  }}</span>
                <p class="mb-0">{{ Str::limit($candidateEducation->institute,50,'...')  }}</p>
            </div>
        </article>
    </div>
@endforeach
