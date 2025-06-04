<div id="showPostCommentModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3>{{ __('messages.post_comment.post_comment_details') }}</h3>
                <button type="button" aria-label="Close" class="px-4 py-2 rounded font-medium transition-colors -close"
                        data-bs-dismiss="modal">
                </button>
            </div>
            {{ Form::open(['id' => 'showPostCommentsForm']) }}
            <div class="modal-body">
                <div class="alert p-4 rounded-md mb-4 -danger  hide hidden" id="maritalStatusValidationErrorsBox">
                    <i class="fa-solid fa-face-frown me-5"></i>
                </div>
                <div class="mb-5">
                    {{ Form::label('title',__('messages.post.post').(':'), ['class' => 'pb-2 fs-5 text-gray-600']) }}
                    <p id="postTitle"></p>
                </div>
                <div class="mb-5">
                    {{ Form::label('comment',__('messages.post.comment').(':'),['class' => 'pb-2 fs-5 text-gray-600']) }}
                    <p id="postComment"></p>
                </div>
                <div class="mb-5">
                    {{ Form::label('username',__('messages.user.user_name').(':'),['class' => 'pb-2 fs-5 text-gray-600']) }}
                    <p id="postUser"></p>
                </div>
                <div class="mb-5">
                    {{ Form::label('email',__('messages.common.email').(':'),['class' => 'pb-2 fs-5 text-gray-600']) }}
                    <p id="postEmail"></p>
                </div>
                <div class="mb-5">
                    {{ Form::label('title',__('messages.common.created_on').(':'),['class' => 'pb-2 fs-5 text-gray-600']) }}
                    <p id="postCreatedOn"></p>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

