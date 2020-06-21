<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;

class ConditionalUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return request()->route('tag')->user->is(auth()->user())
            && request()->route('tag')->is(request()->route('condition')->conditionable);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'parameter' => 'string',
            'comparator' => 'string',
            'value' => 'string',
        ];
    }
}
