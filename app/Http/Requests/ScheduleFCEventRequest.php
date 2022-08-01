<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleFCEventRequest extends FormRequest
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
            'title' => 'required|min:3',
            'start' => 'date_format:Y-m-d H:i:s|before:end',
            'end' => 'date_format:Y-m-d H:i:s|after:start',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Fill in the Title field',
            'title.min' => 'Title needs to be at least 03 characters long',
            'start.date_format' => 'Fill in a valid Start Date',
            'start.before' => 'Start Date/Time must be less than End Date',
            'end.date_format' => 'Fill End Date with a valid date',
            'end.after' => 'End Date/Time must be greater than Start Date',
        ];
    }
}
