<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCayCanhRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'ten_cay' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:danh_muc,category_id',
            ],

            'gia' => [
                'required',
                'numeric',
                'min:0',
            ],

            'so_luong' => [
                'required',
                'integer',
                'min:0',
            ],

            'mo_ta' => [
                'nullable',
                'string',
            ],

            'cach_cham_soc' => [
                'nullable',
                'string',
            ],

            'chieu_cao' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^\d+(?:\.\d+)?(?:\s*-\s*\d+(?:\.\d+)?)?(?:\s*cm)?$/iu',
            ],

            'anh_dai_dien' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'trang_thai' => [
                'required',
                'in:Đang bán,Ẩn',
            ],
        ];
    }


    public function messages(): array
    {
        return [
            'ten_cay.required' =>
                'Vui lòng nhập tên cây.',

            'ten_cay.max' =>
                'Tên cây không được vượt quá 255 ký tự.',

            'category_id.required' =>
                'Vui lòng chọn danh mục.',

            'category_id.exists' =>
                'Danh mục không hợp lệ.',

            'gia.required' =>
                'Vui lòng nhập giá bán.',

            'gia.numeric' =>
                'Giá bán phải là số.',

            'gia.min' =>
                'Giá bán không được âm.',

            'so_luong.required' =>
                'Vui lòng nhập số lượng tồn kho.',

            'so_luong.integer' =>
                'Số lượng phải là số nguyên.',

            'so_luong.min' =>
                'Số lượng không được âm.',

            'chieu_cao.max' =>
                'Chiều cao không được vượt quá 50 ký tự.',

            'chieu_cao.regex' =>
                'Chiều cao phải có dạng số hoặc khoảng, ví dụ 40, 30-50 hoặc 30-50 cm.',

            'anh_dai_dien.image' =>
                'File tải lên phải là hình ảnh.',

            'anh_dai_dien.mimes' =>
                'Ảnh chỉ hỗ trợ JPG, JPEG, PNG hoặc WEBP.',

            'anh_dai_dien.max' =>
                'Ảnh không được vượt quá 5MB.',

            'trang_thai.required' =>
                'Vui lòng chọn trạng thái.',

            'trang_thai.in' =>
                'Trạng thái không hợp lệ.',
        ];
    }


    protected function prepareForValidation(): void
    {
        $height = trim((string) $this->chieu_cao);
        $height = str_replace(',', '.', $height);
        $height = preg_replace('/\s+/', ' ', $height) ?? $height;

        $this->merge([
            'ten_cay' => trim((string) $this->ten_cay),
            'chieu_cao' => $height !== '' ? $height : null,
        ]);
    }
}