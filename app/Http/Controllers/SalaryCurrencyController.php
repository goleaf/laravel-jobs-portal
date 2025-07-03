<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSalaryCurrencyRequest;
use App\Http\Requests\UpdateSalaryCurrencyRequest;
use App\Models\Candidate;
use App\Models\Job;
use App\Models\Plan;
use App\Models\SalaryCurrency;
use App\Repositories\SalaryCurrencyRepository;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SalaryCurrencyController extends AppBaseController
{
    /** @var SalaryCurrencyRepository */
    private $salaryCurrencyRepository;

    public function __construct(SalaryCurrencyRepository $salaryCurrencyRepo)
    {
        $this->salaryCurrencyRepository = $salaryCurrencyRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Factory|View
     */
    public function index(): View
    {
        return view('salary_currencies.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSalaryCurrencyRequest $request): JsonResponse
    {
        $input = $request->all();
        $this->salaryCurrencyRepository->create($input);

        return $this->sendSuccess(__('messages.flash.salary_currency_store'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalaryCurrency $currency): JsonResponse
    {
        return $this->sendResponse($currency, __('messages.flash.salary_currency_edit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $currencyId
     */
    public function update(UpdateSalaryCurrencyRequest $request, $currencyId): JsonResponse
    {
        $input = $request->all();
        $this->salaryCurrencyRepository->update($input, $currencyId);

        return $this->sendSuccess(__('messages.flash.salary_currency_update'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalaryCurrency $currency): JsonResponse
    {
        $model = [
            Plan::class,
        ];

        $plan = Plan::all()->pluck('salary_currency_id')->toArray();
        $used_currency = array_values(value(array_unique($plan)));

        $currency_exists = in_array($currency->id, $used_currency);

        if ($currency_exists) {
            return $this->sendError(__('messages.flash.salary_currency_already_in_use'));
        }

        $result = canDelete($model, 'salary_currency_id', $currency->id);
        if (! $result) {
            $result = canDelete([Job::class], 'currency_id', $currency->id);
        }
        if (! $result) {
            $result = canDelete([Candidate::class], 'salary_currency', $currency->id);
        }
        if ($result) {
            return $this->sendError(__('messages.flash.salary_currency_cant_delete'));
        }
        $currency->delete();

        return $this->sendSuccess(__('messages.flash.salary_currency_destroy'));
    }
}
