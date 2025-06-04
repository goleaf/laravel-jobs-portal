<?php
        $isJobClosed = false;
        $isJobPause = false;
        $isJobDraft = false;
        if ($row->status == 2) {
            $isJobClosed = true;
        }
        if ($row->status == 3) {
            $isJobPause = true;
        }
        if ($row->status == 0) {
            $isJobDraft = true;
        }

$statusArray = App\Models\Job::STATUS;
?>

@if(!$isJobClosed)
    @if($statusArray[$row->status] == 'Drafted')
        <button class="px-4 py-2 rounded font-medium transition-colors bg-gray-100 -warning mr-1 badge job-application-status"
                style="cursor:context-menu"><?php echo __('messages.common.drafted') ?></button>
    @else
        <div class="relative inline-block text-left dropdown-transparent">
            {{--            <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out bg-gray-100 text-gray-800 hover:bg-gray-200 btn-active-light-primary px-4 py-2 rounded font-medium transition-colors -sm inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50" data-bs-toggle="dropdown"--}}
            {{--               aria-expanded="false">--}}
            {{--                {{$statusArray[$row->status]}}--}}
            {{--            </a>--}}

            <button class="px-4 py-2 rounded font-medium transition-colors inline-flex justify-center w-full rounded-md border border-gray-300 border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 text-gray-600 mr-1" type="button"
                    id="dropdownMenuButton1"
                    data-bs-toggle="dropdown" aria-expanded="false">
                @if($statusArray[$row->status]== 'Live')
                    {{__('messages.common.live')}}
                @else
                    {{__('messages.common.paused')}}
                @endif
                <i class="fa-solid fa-angle-down ms-2"></i>
            </button>

            <ul class="fw-bold fs-6 py-4 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 customDropdown"
                aria-labelledby="dropdownMenuButton1">

                @if($statusArray[$row->status]== 'Live')
                    <li>
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm action-pause change-status" data-id="{{$row->id}}"
                           data-option="Paused"><?php echo __('messages.common.paused') ?></a>
                    </li>
                    <li>
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm action-close change-status" data-id="{{$row->id}}"
                           data-option="Closed"><?php echo __('messages.common.closed') ?></a>
                    </li>
                @endif
                @if($statusArray[$row->status]== 'Paused')
                    <li>
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm action-open change-status" data-id="{{$row->id}}"
                           data-option="Live"><?php echo __('messages.common.live') ?></a>
                    </li>
                    <li>
                        <a class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -sm action-close change-status" data-id="{{$row->id}}"
                           data-option="Closed"><?php echo __('messages.common.closed') ?></a>
                    </li>
                @endif
            </ul>
        </div>
    @endif

@else
    <button class="inline-flex items-center px-4 py-2 border border-gray-300 border-transparent text-sm font-medium rounded-md transition duration-150 ease-in-out px-4 py-2 rounded font-medium transition-colors -danger mr-1 badge job-application-status"
            style="cursor:context-menu"><?php echo __('messages.common.closed') ?></button>
@endif
