// view_slot_screen Component
document.addEventListener('DOMContentLoaded', function() {
    try {
        // view_slot_screen Component JavaScript
// Enhanced with Universal patterns

var interviewSlotStoreUrl ="{{ route('employer.', ['jobId' => request()->route('jobId.index')]) }}";
        var batchSlotStoreUrl ="{{ route('employer.', ['jobId' => request()->route('jobId.index')]) }}";
        var uniqueId = 1;
        var JobApplicationId ="{{ request()->route('jobApplicationId.index') }}";
        var getScheduleHistory ="{{ route('employer.', ['jobId' => request()->route('jobId.index')]) }}";
        var cancelSlotUrl ="{{ route('employer.', ['jobId' => request()->route('jobId.index')]) }}";
        var jobApplicationUrl ="{{ url('employer/jobs/'.request()->route('jobId.index').'/applications') }}";


    } catch (error) {
        console.error('Error in view_slot_screen component:', error);
    }
});