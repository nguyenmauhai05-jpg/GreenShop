<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DangKyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ho_ten' => trim((string) $this->ho_ten),
            'email' => strtolower(trim((string) $this->email)),
            'so_dien_thoai' => trim((string) $this->so_dien_thoai),
        ]);
    }

    public function rules(): array
    {
        return [
            'ho_ten' => [
                'bail',
                'required',
                'string',
                'min:2',
                'max:150',
                'regex:/\S/u',
            ],

            'email' => [
                'bail',
                'required',
                'string',
                'min:5',
                'max:255',
                'email',
                'unique:nguoi_dung,email',
            ],

            'so_dien_thoai' => [
                'bail',
                'required',
                'string',
                'size:10',
                'regex:/^0[0-9]{9}$/',
            ],

            'mat_khau' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:72',
            ],

            'xac_nhan_mat_khau' => [
                'bail',
                'required',
                'string',
                'min:8',
                'max:72',
                'same:mat_khau',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ho_ten.required' => 'Vui lòng nhập họ tên.',
            'ho_ten.min' => 'Họ tên phải có ít nhất 2 ký tự.',
            'ho_ten.max' => 'Họ tên không được vượt quá 150 ký tự.',
            'ho_ten.regex' => 'Họ tên không được chỉ chứa khoảng trắng.',

            'email.required' => 'Vui lòng nhập email.',
            'email.min' => 'Email không đúng định dạng.',
            'email.email' => 'Email không đúng định dạng.',
            'email.max' => 'Email không được vượt quá 255 ký tự.',
            'email.unique' => 'Email đã được sử dụng. Vui lòng sử dụng email khác.',

            'so_dien_thoai.required' => 'Vui lòng nhập số điện thoại.',
            'so_dien_thoai.size' => 'Số điện thoại phải gồm 10 chữ số.',
            'so_dien_thoai.regex' => 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.',

            'mat_khau.required' => 'Vui lòng nhập mật khẩu.',
            'mat_khau.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'mat_khau.max' => 'Mật khẩu không được vượt quá 72 ký tự.',

            'xac_nhan_mat_khau.required' => 'Vui lòng nhập xác nhận mật khẩu.',
            'xac_nhan_mat_khau.min' => 'Xác nhận mật khẩu phải có ít nhất 8 ký tự.',
            'xac_nhan_mat_khau.max' => 'Xác nhận mật khẩu không được vượt quá 72 ký tự.',
            'xac_nhan_mat_khau.same' => 'Xác nhận mật khẩu không khớp.',
        ];
    }
}
