<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class SubscriberController
 */
class SubscriberController extends AppBaseController
{
    /**
     * @param  Request  $request
     * @return Factory|View
     *
     * @throws Exception
     */
    public function index(): View
    {
        return view('subscribers.index');
    }

    /**
     * Remove the specified NewsLetter from storage.
     *
     *
     * @throws Exception
     */
    public function destroy(NewsLetter $newsLetter): JsonResponse
    {
        $newsLetter->delete();

        return $this->sendSuccess(__('messages.flash.newsletter_delete'));
    }
}
