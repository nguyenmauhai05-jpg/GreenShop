<?php

namespace App\Services\Shop;

use Illuminate\Http\Request;

class ShopFilterService
{
    /**
     * Đọc và kiểm tra bộ lọc trang Cửa hàng.
     *
     * Quy tắc cũ được giữ nguyên: nếu bất kỳ filter nào không hợp lệ thì
     * không áp dụng các filter category/price/status/size, nhưng từ khóa
     * tìm kiếm vẫn được giữ.
     *
     * @return array<string, mixed>
     */
    public function resolve(Request $request): array
    {
        $keyword = trim((string) $request->input('search', ''));
        $category = $request->input('category');
        $minPriceInput = $request->input('min_price');
        $maxPriceInput = $request->input('max_price');
        // Bộ lọc Còn hàng/Hết hàng đã được bỏ khỏi trang Cửa hàng.
        $statuses = [];
        $sizes = array_values(array_filter((array) $request->input('size', [])));
        $filterErrors = [];

        if ($category !== null && $category !== '' && ! ctype_digit((string) $category)) {
            $filterErrors['category'] = 'Danh mục được chọn không hợp lệ.';
        }

        $minPrice = $this->parsePrice(
            $minPriceInput,
            'min_price',
            'Giá từ phải là một số hợp lệ.',
            'Giá từ không được nhỏ hơn 0.',
            $filterErrors
        );

        $maxPrice = $this->parsePrice(
            $maxPriceInput,
            'max_price',
            'Giá đến phải là một số hợp lệ.',
            'Giá đến không được nhỏ hơn 0.',
            $filterErrors
        );

        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            $filterErrors['price'] = 'Giá từ không được lớn hơn Giá đến.';
        }

        $this->validateSelections(
            $sizes,
            ['small', 'medium', 'large'],
            'size',
            'Kích thước cây không hợp lệ.',
            $filterErrors
        );

        return [
            'keyword' => $keyword,
            'category' => $category,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'statuses' => $statuses,
            'sizes' => $sizes,
            'sort' => (string) $request->input('sort', 'newest'),
            'filterErrors' => $filterErrors,
            'filtersValid' => $filterErrors === [],
        ];
    }

    /**
     * @param array<string, string> $errors
     */
    private function parsePrice(
        mixed $value,
        string $key,
        string $invalidMessage,
        string $negativeMessage,
        array &$errors
    ): ?float {
        if ($value === null || $value === '') {
            return null;
        }

        if (! is_numeric($value)) {
            $errors[$key] = $invalidMessage;

            return null;
        }

        $price = (float) $value;

        if ($price < 0) {
            $errors[$key] = $negativeMessage;
        }

        return $price;
    }

    /**
     * @param array<int, mixed> $values
     * @param array<int, string> $allowed
     * @param array<string, string> $errors
     */
    private function validateSelections(
        array $values,
        array $allowed,
        string $errorKey,
        string $message,
        array &$errors
    ): void {
        foreach ($values as $value) {
            if (! in_array($value, $allowed, true)) {
                $errors[$errorKey] = $message;

                return;
            }
        }
    }
}
