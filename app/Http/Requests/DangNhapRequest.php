<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangNhapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->email)),
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => ['bail', 'required', 'email', 'max:255'],
            'mat_khau' => ['bail', 'required', 'string', 'max:72'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.max' => 'Mật khẩu không được vượt quá 72 ký tự.',
        ];
    }
}
