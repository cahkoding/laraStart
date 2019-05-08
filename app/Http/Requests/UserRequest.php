<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => 'required|max:255',
            'type' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'sometimes|required|min:6|confirmed',
            'photo' => 'sometimes'
        ];

        if($this->id){
            $rules['email'] = 'sometimes|required|unique:users,email,'.$this->id;
        }

        return $rules;
    }
}
