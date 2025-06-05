<script id="scheduleSlotBookHtmlTemplate" type="text/x-jsrender">
    <div class="shadow rounded mb-5 slot-box dark-background">
                        <div class="flex-wrap p-5 flex -mx-4">
                            <div class="mb-2 flex-1 px-4 -sm-12">
                                <span class="fw-bold fs-5">{{:schedule_date}} - {{:schedule_time}}</span>
                            </div>
                            <div class="mb-2 flex-1 px-4 -sm-12">
                                <span class="fw-bold fs-5">{{:notes}}</span>
                            </div>
                            <div class="mb-0 flex-1 px-4 -sm-12">
                                <div class="form-check form-check-custom flex items-center -solid is-valid flex items-center -sm">
                                    <input type="radio" name="slot_book" data-schedule="{{:schedule_id}}" id="{{:index}}" class="rounded border border border-gray-300 -gray-300 h-4 w-4 text-indigo-600 focus:ring-indigo-500 -gray-300 slot-book me-3" value="<?php echo \App\Models\JobApplicationSchedule::STATUS_SEND ?>">
                                    <label class="custom-control-label fw-bold fs-5" for="{{:index}}"><?php echo __('messages.job_stage.slot_preference') ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

</script>
<script id="chooseSlotHistoryHtmlTemplate" type="text/x-jsrender">
<div class="shadow rounded p-5 mb-5 dark-background">
    <div class="justify-between flex">
          <span class="fw-bold fs-5">{{:companyName}}</span>
          <span class="fw-bold fs-5">{{:schedule_created_at}}</span>
     </div>
     <span class="fw-bold fs-5">{{:notes}}</span>
     </div>

</script>

<script id="selectedSlotBookHtmlTemplate" type="text/x-jsrender">
    <div class="shadow rounded mb-5 bg-green-600 slot-box">
                        <div class="flex-wrap p-5 flex -mx-4">
                            <div class="flex-1 px-4 -sm-12">
                                <span class="fw-bold fs-5">{{:schedule_date}} - {{:schedule_time}}</span>
                            </div>
                            <div class="flex-1 px-4 -sm-12">
                                <span class="fw-bold fs-5">{{:notes}}</span>
                            </div>
                        </div>
                    </div>

</script>
