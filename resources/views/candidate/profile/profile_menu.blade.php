<div class="overflow-hidden">
    <ul class="overflow-auto flex-nowrap whitespace-nowrap flex space-x-1 flex space-x-8-tabs" id="subAnalytics" role="tablist">
        <li class="mb-3 flex space-x-8-item relative me-7" role="presentation">
            <a class="rounded p-0 text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium {{ (isset($data["sectionName']) && $data['sectionName'] == 'general') ? 'active' : '' }} fs-5"
               href="{{ route('candidate.',['section' => 'general']) }}"
               tabindex="-1">{{ __('messages.general') }}</a>
        </li>
        <li class="mb-3 flex space-x-8-item relative me-7" role="presentation">
            <a class="rounded p-0 text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium {{ (isset($data["sectionName']) && $data['sectionName'] == 'resume') ? 'active' : '' }} fs-5"
               href="{{ route('candidate.',['section' => 'resume']) }}">  {{ __('messages.apply_job.resume') }}</a>
        </li>
        <li class="mb-3 flex space-x-8-item relative me-7" role="presentation">
            <a class="rounded p-0 text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium {{ ( isset($data["sectionName']) && $data['sectionName'] == 'career-informations') ?  'active' : '' }} fs-5"
               href="{{ route('candidate.',['section' => 'career-informations']) }}">  {{ __('messages.career_informations') }}</a>
        </li>
        <li class="mb-3 flex space-x-8-item relative me-7" role="presentation">
            <a class="rounded p-0 text-gray-600 hover:text-gray-900 px-3 py-2 -md text-sm font-medium {{ (isset($data["sectionName']) && $data['sectionName'] == 'cv-builder') ? 'active' : '' }} fs-5"
               href="{{ route('candidate.',['section' => 'cv-builder']) }}"> {{ __('messages.cv_builder') }}</a>
        </li>
    </ul>
</div>


