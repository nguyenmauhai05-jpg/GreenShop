<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDanhMucRequest extends FormRequest
{
    protected $errorBag = 'editCategory';
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'ten_danh_muc' => trim((string) $this->ten_danh_muc),
            'mo_ta' => $this->mo_ta !== null
                ? trim((string) $this->mo_ta)
                : null,
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'ten_danh_muc' => [
                'bail',
                'required',
                'string',
                'regex:/\S/u',
                'max:100',

                Rule::unique(
                    'danh_muc',
                    'ten_danh_muc'
                )->ignore(
                    $id,
                    'category_id'
                ),
            ],

            'mo_ta' => [
                'nullable',
                'string',
                'max:255',
            ],

            'trang_thai' => [
                'required',

                Rule::in([
                    'Hiển thị',
                    'Ẩn',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ten_danh_muc.required' =>
                'Tên danh mục là bắt buộc và không được chỉ chứa khoảng trắng.',

            'ten_danh_muc.string' =>
                'Tên danh mục không hợp lệ.',

            'ten_danh_muc.regex' =>
                'Tên danh mục không được chỉ chứa khoảng trắng.',

            'ten_danh_muc.max' =>
                'Tên danh mục không được vượt quá 100 ký tự.',

            'ten_danh_muc.unique' =>
                'Tên danh mục đã tồn tại trong hệ thống.',

            'mo_ta.string' =>
                'Mô tả không hợp lệ.',

            'mo_ta.max' =>
                'Mô tả không được vượt quá 255 ký tự.',

            'trang_thai.required' =>
                'Vui lòng chọn trạng thái.',

            'trang_thai.in' =>
                'Trạng thái không hợp lệ.',
        ];
    }
}