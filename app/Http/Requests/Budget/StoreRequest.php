<?php

namespace App\Http\Requests\Budget;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use NotificationChannels\Discord\DiscordMessage;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'title' => 'string|max:180|nullable',
            'body' => 'string|max:260|nullable',
            'channels' => 'required|array',
            'payload' => 'json|nullable',
            'events' => 'array',
            'webhook_url' => [
                new RequiredIf(
                    in_array(SlackMessage::class, request()->get('channels', null))
                    || in_array(DiscordMessage::class, request()->get('channels', null))
                )
            ]
        ];
    }
}
