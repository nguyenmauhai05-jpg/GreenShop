<?php if (! $__env->hasRenderedOnce('1cca71e7-7492-4a6c-819e-d92809c426b6')): $__env->markAsRenderedOnce('1cca71e7-7492-4a6c-819e-d92809c426b6'); ?>
    <?php
        $systemToastMessages = collect([
            ['type' => 'success', 'message' => session('success')],
            ['type' => 'warning', 'message' => session('warning')],
            ['type' => 'error', 'message' => session('error')],
            ['type' => 'info', 'message' => session('info')],
            ['type' => 'success', 'message' => session('payment_notice')],
        ])->filter(fn ($item) => filled($item['message']))->values();

        if ($errors->any() && !$systemToastMessages->contains(
            fn ($item) => $item['type'] === 'error' && $item['message'] === $errors->first()
        )) {
            $systemToastMessages->push([
                'type' => 'error',
                'message' => $errors->first(),
            ]);
        }

        $systemToastJson = json_encode(
            $systemToastMessages->all(),
            JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_HEX_TAG
            | JSON_HEX_AMP
            | JSON_HEX_APOS
            | JSON_HEX_QUOT
        );
    ?>

    <?php echo app('Illuminate\Foundation\Vite')([
        'resources/css/shared/system-toast.css',
        'resources/js/shared/system-toast.js',
    ]); ?>

    <div
        id="gsSystemToastRegion"
        class="gs-system-toast-region"
        aria-live="polite"
        aria-atomic="false"
    ></div>

    <script type="application/json" id="gsSystemToastData"><?php echo $systemToastJson; ?></script>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/components/system-toast.blade.php ENDPATH**/ ?>