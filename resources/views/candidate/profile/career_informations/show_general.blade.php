<div class="d-md-flex block justify-between">
    <h1 id="candidateName" class="text-primary-600">{{ $user->full_name }}</h1>
    <a href="javascript:void(0)" class="editGeneralBtn"><i class="fas fa-user-edit fa-2x text-primary-600"></i></a>
</div>
<div class="mt-3">
    @isset($user->candidate->full_location)
        <p class="mb-1 text-gray-500" id="candidateLocation">{{ $user->candidate->full_location}}</p>
    @endisset
    <p class="mb-1 text-gray-500" id="cadidateEmail">{{ $user->email}}</p>
    <p id="candidatePhone" class="text-gray-500">{{ $user->phone}}</p>
</div>
<div class="my-5 border-2 border-bottom border-danger px-5">
    <h5 class="mt-2 fs-2 text-info"><i
                class="fas fa-list-ul text-info p-3 border rounded-circle border-info me-3"></i>{{ __('messages.candidate.candidate_skill') }}
    </h5>
</div>
<div id="candidateSkillDiv" class="px-6">
    @if($user->candidateSkill)
        <ul class="pl-3">
            @foreach($user->candidateSkill as $skill)
                <li class="text-gray-500">{{ $skill->name }}</li>
            @endforeach
        </ul>
    @endif
</div>
