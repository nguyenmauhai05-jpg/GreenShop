<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\GreenShopSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CaiDatHeThongController extends Controller
{
    public function index()
    {
        return view('admin.cai_dat_he_thong.index', [
            'settings' => GreenShopSettings::all(),
            'mailDriverOptions' => [
                'smtp' => 'SMTP',
                'log' => 'LOG',
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => ['bail', 'required', 'string', 'regex:/\S/u', 'max:100'],
            'contact_email' => ['bail', 'required', 'email:rfc', 'max:255'],
            'phone' => ['bail', 'required', 'regex:/^0[0-9]{9}$/'],
            'address' => ['required', 'string', 'max:255'],
            'store_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'store_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'description' => ['nullable', 'string', 'max:500'],

            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],


            'mail_driver' => ['required', Rule::in(['smtp', 'log'])],
            'mail_host' => [
                'required_if:mail_driver,smtp',
                'nullable',
                'string',
                'max:255',
                'regex:/^(?=.{1,255}$)(?:localhost|(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,63}|(?:\d{1,3}\.){3}\d{1,3})$/',
            ],
            'mail_port' => ['required_if:mail_driver,smtp', 'nullable', 'integer', 'between:1,65535'],
            'mail_from' => ['required', 'email:rfc', 'max:255'],
            'mail_auth' => ['nullable', 'boolean'],

            'maintenance_mode' => ['nullable', 'boolean'],
            'confirm_maintenance' => ['nullable', 'accepted_if:maintenance_mode,1'],
        ], [
            'store_name.required' => 'Tên cửa hàng không được để trống.',
            'store_name.regex' => 'Tên cửa hàng không được chỉ chứa khoảng trắng.',
            'store_name.max' => 'Tên cửa hàng tối đa 100 ký tự.',
            'contact_email.required' => 'Email liên hệ không được để trống.',
            'contact_email.email' => 'Email liên hệ không đúng định dạng.',
            'phone.required' => 'Số điện thoại không được để trống.',
            'phone.regex' => 'Số điện thoại phải có đúng 10 chữ số, bắt đầu bằng 0 và không được chứa chữ hoặc ký tự đặc biệt.',
            'address.required' => 'Địa chỉ không được để trống.',
            'address.max' => 'Địa chỉ tối đa 255 ký tự.',
            'store_latitude.numeric' => 'Vĩ độ cửa hàng không hợp lệ.',
            'store_latitude.between' => 'Vĩ độ cửa hàng không hợp lệ.',
            'store_longitude.numeric' => 'Kinh độ cửa hàng không hợp lệ.',
            'store_longitude.between' => 'Kinh độ cửa hàng không hợp lệ.',
            'description.string' => 'Mô tả cửa hàng không hợp lệ.',
            'description.max' => 'Mô tả cửa hàng tối đa 500 ký tự.',
            'logo.image' => 'Logo phải là tệp hình ảnh.',
            'logo.mimes' => 'Logo chỉ chấp nhận JPG, JPEG, PNG hoặc WEBP.',
            'logo.max' => 'Logo tối đa 2 MB.',
            'mail_driver.in' => 'Mail Driver không hợp lệ.',
            'mail_host.required_if' => 'Host là bắt buộc khi dùng SMTP.',
            'mail_host.regex' => 'Host không hợp lệ. Ví dụ hợp lệ: smtp.gmail.com, localhost hoặc địa chỉ IP.',
            'mail_port.required_if' => 'Port là bắt buộc khi dùng SMTP.',
            'mail_port.integer' => 'Port phải là số nguyên.',
            'mail_port.between' => 'Port phải nằm trong khoảng 1 đến 65535.',
            'mail_from.required' => 'Email gửi từ không được để trống.',
            'mail_from.email' => 'Email gửi từ không đúng định dạng.',
            'confirm_maintenance.accepted_if' => 'Bạn phải xác nhận trước khi bật chế độ bảo trì.',
        ]);

        try {
            $settings = GreenShopSettings::all();
            $oldLogo = (string) ($settings['logo'] ?? '');

            if ($request->hasFile('logo')) {
                $directory = public_path('uploads/system');

                if (!File::isDirectory($directory)) {
                    File::makeDirectory($directory, 0755, true);
                }

                $file = $request->file('logo');
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = 'greenshop-logo-' . now()->format('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . $extension;

                $file->move($directory, $filename);

                $settings['logo'] = 'uploads/system/' . $filename;

                if (
                    $oldLogo !== ''
                    && str_starts_with($oldLogo, 'uploads/system/')
                    && File::exists(public_path($oldLogo))
                ) {
                    File::delete(public_path($oldLogo));
                }
            }

            $settings = array_replace($settings, [
                'store_name' => trim($validated['store_name']),
                'contact_email' => trim($validated['contact_email']),
                'phone' => trim($validated['phone']),
                'address' => trim($validated['address']),
                'store_latitude' => isset($validated['store_latitude']) && $validated['store_latitude'] !== null ? (float) $validated['store_latitude'] : null,
                'store_longitude' => isset($validated['store_longitude']) && $validated['store_longitude'] !== null ? (float) $validated['store_longitude'] : null,
                'description' => trim((string) ($validated['description'] ?? '')),


                'mail_driver' => $validated['mail_driver'],
                'mail_host' => trim((string) ($validated['mail_host'] ?? '')),
                'mail_port' => isset($validated['mail_port']) ? (int) $validated['mail_port'] : null,
                'mail_from' => trim($validated['mail_from']),
                'mail_auth' => $request->boolean('mail_auth'),

                'maintenance_mode' => $request->boolean('maintenance_mode'),
            ]);

            GreenShopSettings::save($settings);

            return redirect()
                ->route('admin.cai-dat-he-thong.index')
                ->with('success', 'Cập nhật cài đặt hệ thống thành công.');
        } catch (\Throwable $e) {
            Log::error('Cập nhật cài đặt hệ thống thất bại', [
                'admin_id' => Auth::id(),
                'message' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Không thể cập nhật cài đặt hệ thống. Vui lòng thử lại.');
        }
    }


}
