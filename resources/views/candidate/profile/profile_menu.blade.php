<div class="overflow-hidden">
    <ul class="flex space-x-1 nav-tabs overflow-auto flex-nowrap text-nowrap" id="subAnalytics" role="tablist">
        <li class="nav-item relative me-7 mb-3" role="presentation">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium p-0 {{ (isset($data["sectionName']) && $data['sectionName'] == 'general') ? 'active' : ''}} fs-5"
               href="{{ route('candidate.profile',['section' => 'general']) }}"
               tabindex="-1">{{ __('messages.general') }}</a>
        </li>
        <li class="nav-item relative me-7 mb-3" role="presentation">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium p-0  {{ (isset($data["sectionName']) && $data['sectionName'] == 'resume') ? 'active' : ''}} fs-5"
               href="{{ route('candidate.profile',['section' => 'resume']) }}">  {{ __('messages.apply_job.resume') }}</a>
        </li>
        <li class="nav-item relative me-7 mb-3" role="presentation">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium p-0 {{( isset($data["sectionName']) && $data['sectionName'] == 'career-informations') ?  'active' : '' }} fs-5"
               href="{{  route('candidate.profile',['section' => 'career-informations']) }}">  {{ __('messages.career_informations') }}</a>
        </li>
        <li class="nav-item relative me-7 mb-3" role="presentation">
            <a class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium p-0 {{ (isset($data["sectionName']) && $data['sectionName'] == 'cv-builder') ? 'active' : ''}} fs-5"
               href="{{ route('candidate.profile',['section' => 'cv-builder']) }}"> {{  __('messages.cv_builder') }}</a>
        </li>
    </ul>
</div>


