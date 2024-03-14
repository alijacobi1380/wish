<?php

namespace App\Http\Requests\Base;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required',
            'lastname' => 'required',
            'phone' => 'required|numeric|min:11|unique:users,phone',
            'role' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ];
    }

    function messages()
    {
        return [
            'name.required' => 'لطفا نام را وارد نمایید',
            'lastname.required' => 'لطفا نام خانوادگی را وارد نمایید',
            'phone.required' => 'لطفا تلفن همراه را وارد نمایید',
            'phone.unique' => 'این شماره تلفن قبلا استفاده شده است',
            'phone.numeric' => 'لطفا تلفن همراه را به صورت صحیح وارد نمایید',
            'phone.min' => 'لطفا تلفن همراه را به صورت صحیح وارد نمایید',
            'role.required' => 'لطفا نقش را وارد نمایید',
            'email.required' => 'لطفا ایمیل را وارد نمایید',
            'email.email' => 'فرمت ایمیل صحیح نیست',
            'email.unique' => 'این ایمیل قبلا استفاده شده است',
            'password.required' => 'رمز عبور را وارد نمایید',
            'password.min' => 'رمز عبور باید حداقل ۸ حرف باشد',
        ];
    }
}
