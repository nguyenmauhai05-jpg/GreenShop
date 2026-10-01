import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

const inputs = [
    'resources/css/shared/system-toast.css',
    'resources/js/shared/system-toast.js',
    'resources/css/app.css',
    'resources/css/customer/site-shell.css',
    'resources/css/customer/home.css',
    'resources/css/cham-soc-cay.css',
    'resources/css/chi-tiet-cay.css',
    'resources/css/cua-hang.css',
    'resources/css/gio-hang.css',
    'resources/css/gioi-thieu.css',
    'resources/css/admin.css',
    'resources/css/admin/bao-cao.css',
    'resources/css/admin/cay-canh.css',
    'resources/css/admin/danh-muc.css',
    'resources/css/admin/dashboard.css',
    'resources/css/admin/voucher.css',
    'resources/css/admin/cai-dat-he-thong.css',
    'resources/css/admin/danh-gia.css',
    'resources/css/admin/don-hang.css',
    'resources/css/customer/blog.css',
    'resources/css/customer/danh-gia.css',
    'resources/css/customer/don-hang.css',
    'resources/css/customer/ho-so.css',
    'resources/css/customer/payment-demo.css',
    'resources/css/customer/so-dia-chi.css',
    'resources/css/customer/thanh-toan.css',
    'resources/css/customer/auth-register.css',
    'resources/css/customer/auth-login.css',
    'resources/css/customer/password-reset.css',
    'resources/css/admin/sidebar.css',
    'resources/js/admin/bao-cao.js',
    'resources/js/admin/danh-muc.js',
    'resources/js/admin/cay-canh.js',
    'resources/js/customer/cham-soc-cay.js',
    'resources/js/customer/cua-hang.js',
    'resources/js/customer/danh-gia.js',
    'resources/js/customer/don-hang.js',
    'resources/js/customer/header.js',
    'resources/js/customer/ho-so.js',
    'resources/js/customer/so-dia-chi.js',
    'resources/js/customer/thanh-toan.js',
    'resources/js/admin/cai-dat-he-thong.js',
    'resources/js/admin/voucher.js',
    'resources/js/customer/gio-hang.js',
    'resources/js/customer/auth-password.js',
    'resources/js/customer/chi-tiet-cay.js',
    'resources/js/customer/password-reset.js',
    'resources/js/admin/plant-form.js',
];

export default defineConfig({
    plugins: [
        laravel({
            input: inputs,
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
