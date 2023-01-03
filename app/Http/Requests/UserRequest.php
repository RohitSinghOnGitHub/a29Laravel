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
        return [
            "mobile"=>"required|max:10|min:10|unique:users",
            "password"=>"required|max:20|min:8"
        ];
    }
    public function messages(){
        return [
            "mobile.required"=>"Mobile no must not be empty.",
            "mobile.unique"=>"Mobile no Allready registered.",
            "mobile.max"=>"Mobile no must be of 10 digits.",
            "mobile.min"=>"Mobile no must be of 10 digits."
        ];
    }
}
