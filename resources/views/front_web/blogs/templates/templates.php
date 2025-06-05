<script id="blogTemplate" type="text/x-jsrender">

                <div class="comment-bg-white overflow-hidden shadow rounded-lg bg-white overflow-hidden shadow rounded -lg py-20 mb-40">
                                        <div class="flex flex-wrap justify-between">
                                            <div class="col-xl-1 flex-1 -sm-2 flex-1 -3">
                                                <div class="">
                                                        <img class="bg-white overflow-hidden shadow rounded -lg -img" src="{{:image}}" alt="user-image">
                                                </div>
                                            </div>
                                            <div class="col-xl-6 flex-1 -sm-5 flex-1 -9 ps-xl-4">
                                                <div class="bg-white overflow-hidden shadow rounded -lg -body ps-0">
                                                    <h5 class="bg-white overflow-hidden shadow rounded -lg -title fs-16 text-gray-600">
                                                       {{:commentName}}
                                                            <div class="inline -flex ms-2">
                                                                <a href="javascript:void(0)" title="{{ __('messages.common.edit') }}"
                                                                       class="edit-comment-inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 action- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out" data-id="{{:id}}">
                                                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-indigo-600 py-2 ms-1" data-text="Edit Comment">
                                                                        <span class="fa fa-pencil"></span>
                                                                    </div>
                                                                </a>
                                                               <a href="javascript:void(0)" title="{{ __('messages.common.delete') }}"
                                                                       class="action-inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200 delete-comment- inline-flex items-center px-4 py-2 border border border border-gray-300 -gray-300 -transparent text-sm font-medium rounded -md focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out float-right"
                                                                       data-id="{{:id}}">
                                                                    <div class="inline-flex items-center px-2.5 py-0.5 rounded -full text-xs font-medium bg-indigo-600 py-2 ms-1" data-text="Delete Comment">
                                                                        <span class="fa fa-trash"></span>
                                                                    </div>
                                                                </a>
                                                            </div>
                                                    </h5>
                                                    <p class="fs-16 text-gray" id="comment-{{:id}}">
                                                        {{:comment}}</p>
                                                </div>
                                            </div>
                                            <div class="flex-1 -sm-5 text-end">
                                                <span class="fs-14 text-gray">{{:commentCreated}}</span>
                                            </div>
                                        </div>
                                    </div>


</script>
