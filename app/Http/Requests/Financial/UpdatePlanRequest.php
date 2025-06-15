<?php

namespace App\Http\Requests\Financial;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $plan = $this->route('plan');
        $rules = Plan::$rules;
        $rules['name'] = 'required|max:180|unique:plans,name,'.$plan->id;
        $rules['amount'] = 'sometimes|required|numeric|min:1|max:99999';

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $amount = $this->request->get('amount');
        $this->request->set('amount', removeCommaFromNumbers($amount));
    }
}
