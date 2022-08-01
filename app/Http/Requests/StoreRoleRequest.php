<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        //return true;
        return auth()->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$rule_name_unique = Rule::unique('permissions', 'name');
        if ($this->method() !== 'POST') {//if it is update. put/patch method
            return [
                'role_name'=>['required','max:255'],
                'role_slug'=>['required','max:255'],
                'permission'=>['exists:permissions,name','max:255'],
            ];
        }else{//if it is store. post method
            return [
                'role_name'=>['required','max:255'],
                'role_slug'=>['required','max:255'],
                'permission'=>['exists:permissions,name','max:255'],
            ];
        }
    }

    protected function prepareForValidation()
    {
        //array_map('trim',$this->permission);
        //$string = Str::of($this->permission)->trim();
        //dd($this->permission);
        $strings = explode(',', $this->permission);
        //dd($strings);
        $permission_arr = array();
        foreach($strings as $string){
            $permission_arr[] = trim($string);
        }
        //dd($permission_arr);
        
        $this->merge(['permission' => $permission_arr]);
        
        //dd($this->permission);
        //dd($this->permission);
    }

}
