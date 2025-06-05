<script id="jobNotificationTemplate" type="text/x-jsrender">
    <li class="shadow rounded p-4 mt-4 media notification">
        <div class="pt-1 mb-4 mb-0 md:w-4/12 px-4 flex-1 px-4 -sm-12">
             <label class="form-check form-switch flex items-center -custom flex items-center -solid form-switch-sm">
                    <input type="checkbox" name="job_id[]" class="rounded border border border-gray-300 -gray-300 h-4 w-4 text-indigo-600 focus:ring-indigo-500 -gray-300 notification__checkbox jobCheck" value="{{:job_id}}">
                        <span class="custom-switch-indicator"></span>
                            <a href="{{:jobDetails }}" target="_blank"
                               class="mb-1 media-title notification__title flex items-center -label ms-5 text-decoration-none">{{:job_title}}
                            </a>
            </label>
               <div class="text-time flex items-center -label ms-15">{{:created_by}}</div>
        </div>
    </li>
</script>
