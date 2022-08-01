<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMyRouteRequest extends FormRequest
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
            'origin.*'=>'required|max:255',
            'destination.*'=>'required|max:255',
            'duration'=>'required',
            'ticket_type_id.*'=>'required',
            'price.*'=>'required|integer|max:10000'
        ];
    }

    protected function prepareForValidation()
    {
        //array_map('trim',$this->permission);
        //$string = Str::of($this->permission)->trim();
        //dd($this->permission);

        
        $strings = explode(',', $this->origin);
        $strings2 = explode(',', $this->destination);
        //dd($strings);
        $origin_arr = array();
        foreach($strings as $string){
            $origin_arr[] = trim($string);
        }

        $destination_arr = array();
        foreach($strings2 as $string){
            $destination_arr[] = trim($string);
        }
        //dd($permission_arr);
        
        $this->merge(['origin' => $origin_arr]);
        
        $this->merge(['destination' => $destination_arr]);

        
        //dd($this->permission);
        //dd($this->permission);
    }
}
