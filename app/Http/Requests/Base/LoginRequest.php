<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Unique;



class LoginRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    // function messages()
    // {
    //     return [
    //         'email.required' => 'لطفا ایمیل را وارد نمایید',
    //         'email.email' => 'فرمت ایمیل صحیح نیست',
    //         'password.required' => 'رمز عبور را وارد نمایید',
    //     ];
    // }
}
