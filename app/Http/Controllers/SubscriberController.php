<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Class SubscriberController.
 */
class SubscriberController extends AppBaseController
{
    /**
     * Display a listing of subscribers.
     */
    public function index(): View
    {
        return view('subscribers.index');
    }

    /**
     * Remove the specified subscriber from storage.
     *
     * @param mixed $id
     */
    public function destroy($id): JsonResponse
    {
        $subscriber = NewsLetter::findOrFail($id);
        $subscriber->delete();

        return $this->sendSuccess(__('messages.flash.subscriber_delete'));
    }
}
