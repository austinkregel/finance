<?php
declare(strict_types=1);

namespace App\Http\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;

class ConditionalsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return request()->route('alert')->user->is(auth()->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parameter' => 'required',
            'comparator' => 'required',
            'value' => 'required',
        ];
    }
}
