# GD2 - Changed files

## Moved to Vite-managed resources
- `public/css/blog.css` -> `resources/css/customer/blog.css`
- `public/css/danh-gia.css` -> `resources/css/customer/danh-gia.css`
- `public/css/don-hang.css` -> `resources/css/customer/don-hang.css`
- `public/css/ho-so.css` -> `resources/css/customer/ho-so.css`
- `public/css/payment-demo.css` -> `resources/css/customer/payment-demo.css`
- `public/css/so-dia-chi.css` -> `resources/css/customer/so-dia-chi.css`
- `public/css/thanh-toan.css` -> `resources/css/customer/thanh-toan.css`
- `public/css/admin-cai-dat-he-thong.css` -> `resources/css/admin/cai-dat-he-thong.css`
- `public/css/admin-danh-gia.css` -> `resources/css/admin/danh-gia.css`
- `public/css/admin-don-hang.css` -> `resources/css/admin/don-hang.css`
- `public/js/*.js` -> `resources/js/customer/*.js`
- `public/js/admin/bao-cao.js` -> `resources/js/admin/bao-cao.js`

## Added
- `app/Services/Checkout/ShippingPricingService.php`
- `app/Services/Checkout/CheckoutIntegrityService.php`
- `database/README_MYSQL.md`
- `docs/GD2_CHANGED_FILES.md`

## Updated
- `app/Http/Controllers/ThanhToanController.php`
- `vite.config.js`
- Blade files that referenced CSS/JS from `public/`
- `database/greenshop_required_updates.sql`
- `docs/REFACTOR_STATUS.md`

## Removed
- `database/migrations/`
- `database/factories/`
- `database/seeders/`
- old SQL patch scripts whose structure is already present in Dump(1).sql
- obsolete `public/css/vietnamese-font-fix.css`
- old `public/css/` and `public/js/` source trees after moving first-party assets to `resources/`
