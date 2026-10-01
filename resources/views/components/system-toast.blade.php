@once
    @php
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
    @endphp

    @vite([
        'resources/css/shared/system-toast.css',
        'resources/js/shared/system-toast.js',
    ])

    <div
        id="gsSystemToastRegion"
        class="gs-system-toast-region"
        aria-live="polite"
        aria-atomic="false"
    ></div>

    <script type="application/json" id="gsSystemToastData">{!! $systemToastJson !!}</script>
@endonce
