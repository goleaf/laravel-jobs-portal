<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class TagController extends AppBaseController
{
    /** @var TagRepository */
    private $tagRepository;

    public function __construct(TagRepository $tagRepo)
    {
        $this->tagRepository = $tagRepo;
    }

    /**
     * Display a listing of the JobTag.
     *
     * @return Factory|View
     *
     * @throws \Exception
     */
    public function index(): View
    {
        return view('job_tags.index');
    }

    /**
     * Store a newly created Tag in storage.
     */
    public function store(CreateTagRequest $request): JsonResponse
    {
        $input = $request->all();
        $tag = $this->tagRepository->create($input);

        return $this->sendResponse($tag, __('messages.flash.tag_save'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): JsonResponse
    {
        return $this->sendResponse($tag, __('messages.flash.job_tag_retrieve'));
    }

    /**
     * Show the form for editing the specified JobTag.
     */
    public function show(Tag $tag): JsonResponse
    {
        return $this->sendResponse($tag, __('messages.flash.job_tag_retrieve'));
    }

    /**
     * Update the specified Tag in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $input = $request->all();
        $this->tagRepository->update($input, $tag->id);

        return $this->sendSuccess(__('messages.flash.tag_update'));
    }

    /**
     * Remove the specified JobTag from storage.
     *
     * @throws \Exception
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $jobTag = $tag->jobs()->pluck('tag_id')->toArray();
        if (in_array($tag->id, $jobTag)) {
            return $this->sendError(__('messages.flash.job_tag_cant_delete'));
        }
        $tag->delete();

        return $this->sendSuccess(__('messages.flash.job_tag_delete'));
    }
}
