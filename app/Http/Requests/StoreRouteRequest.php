<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRouteRequest extends FormRequest
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
        // if(!Auth::user()->hasRole('merchant')){
        //     return [
        //         'origin.*'=>'required|max:255',
        //         'destination.*'=>'required|max:255',
        //         'duration'=>'required',
        //         'ticket_type_id.*'=>'required',
        //         'price.*'=>'required|integer|max:10000'
        //     ];
        // }else{
        //     return [
        //         'user_id'=>'required',
        //         'origin.*'=>'required|max:255',
        //         'destination.*'=>'required|max:255',
        //         'duration'=>'required',
        //         'ticket_type_id.*'=>'required',
        //         'price.*'=>'required|integer|max:10000'
        //     ];
        // }    
        
        if(!Auth::user()->hasRole('merchant')){
            return [
                'origin.*'=>'required|max:255',
                'destination.*'=>'required|max:255',
                'route_name'=>'required|max:255',
                'ticket_type_id.*'=>'required',
                'price.*'=>'required|integer|max:10000',
                'custom_ticket_id.*'=>'required',
                'custom_ticket_price.*'=> 'required|integer|max:10000',
                //'departure_time.*' => 'required'
            ];
        }else{
            return [
                'user_id'=>'required',
                'origin.*'=>'required|max:255',
                'destination.*'=>'required|max:255',
                'route_name'=>'required|max:255',
                'ticket_type_id.*'=>'required',
                'price.*'=>'required|integer|max:10000',
                'custom_ticket_id.*'=>'required',
                'custom_ticket_price.*'=> 'required|integer|max:10000',
                //'departure_time.*' => 'required'
            ];
        }    
        //dd("works2");
    }

    protected function prepareForValidation()
    {
        //array_map('trim',$this->permission);
        //$string = Str::of($this->permission)->trim();
        //dd($this->permission);
        //dd($this->customTicketCount);
        //dd($this->origin);
        //$strings = explode(',', $this->origin);
        //$strings2 = explode(',', $this->destination);
        //dd($strings);
        //$origin_arr = array();
        //foreach($strings as $string){
            //$origin_arr[] = trim($string);
        //}

        //$destination_arr = array();
        //foreach($strings2 as $string){
        //    $destination_arr[] = trim($string);
        //}
        //dd($permission_arr);
        
        //$this->merge(['origin' => $origin_arr]);
        
        //$this->merge(['destination' => $destination_arr]);

        
        //dd($this->permission);
        //dd($this->permission);
    }
}
