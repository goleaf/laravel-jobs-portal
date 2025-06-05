<div class="md:flex block justify-between">
    <h1 id="candidateName" class="text-indigo-600 -600">{{ $user->full_name }}</h1>
    <a href="javascript:void(0)" class="editGeneralBtn"><i class="text-indigo-600 fas fa-user-edit fa-2x -600"></i></a>
</div>
<div class="mt-3">
    @isset($user->$candidate->full_location)
        <p class="mb-1 text-gray-500" id="candidateLocation">{{ $user->$candidate->full_location }}</p>
    @endisset
    <p class="mb-1 text-gray-500" id="cadidateEmail">{{ $user->email }}</p>
    <p id="candidatePhone" class="text-gray-500">{{ $user->phone }}</p>
</div>
<div class="border border border -b my-5 -2 -red-600 px-5">
    <h5 class="mt-2 fs-2 text-blue-500"><i
                class="rounded border p-3 border border fas fa-list-ul text-blue-500 -gray-300 -circle -info me-3"></i>{{ __('messages.candidate.candidate_skill') }}
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
