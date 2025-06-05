<script id="blogTemplate" type="text/x-jsrender">

<div class="comment- bg-white overflow-hidden shadow rounded-lg bg-white overflow-hidden shadow rounded -lg py-20 mb-40">
    <div class="flex flex-sm- flex flex-wrap justify-between items-start">
        <div class="flex items-center me-2">
            <div class="bg-white overflow-hidden shadow rounded -lg -img me-4">
                <img class="bg-white overflow-hidden shadow rounded -lg -img" src="{{:image}}" alt="user-image">
            </div>
            <div class="">
                <div class="bg-white overflow-hidden shadow rounded -lg -body p-0">
                    <h5 class="bg-white overflow-hidden shadow rounded -lg -title w-100 fs-16 text-gray-600 text-break">
                    {{:commentName}}
                    </h5>
                    <p class="fs-16 text-gray mb-0 text-break"
                        id="comment-{{ $commentRecord->id }}">
                        {{:comment}}
                    </p>
                </div>
            </div>
        </div>
        {{if user}}
        <div class="">
            <div class="inline -flex ms-2 mt-2">
                <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
                        class="edit-comment- inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out action- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out" data-id="{{:id}}">
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium text-indigo-600 py-2 ms-1" data-text="Edit Comment">
                        <span class="fa fa-pencil"></span>
                    </div>
                </a>
                <a href="javascript:void(0)" title="{{ __('messages.common.delete') }}"
                        class="action- inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out delete-comment- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out float-right"
                        data-id="{{:id}}">
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium text-red-600 py-2 ms-1" data-text="Delete Comment">
                        <span class="fa fa-trash"></span>
                    </div>
                </a>
            </div>
        </div>
        {{/if}}
    </div>
    <div class="text-end text-nowrap">
        <span class="fs-14 text-gray">{{:commentCreated}}</span>
    </div>
</div>

</script>
