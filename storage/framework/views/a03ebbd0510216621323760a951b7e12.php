<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['paginator']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['paginator']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php if(is_object($paginator) && method_exists($paginator, 'total') && $paginator->total() > 0): ?>
    <?php
        $current = $paginator->currentPage();
        $last = max(1, $paginator->lastPage());
        $pages = [];

        for ($page = 1; $page <= $last; $page++) {
            if ($page === 1 || $page === $last || abs($page - $current) <= 1) {
                $pages[] = $page;
            }
        }
    ?>

    <nav class="gs-pagination" aria-label="Phân trang">
        <?php if($paginator->onFirstPage()): ?>
            <span class="gs-page-btn is-disabled" aria-disabled="true" aria-label="Trang trước">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </span>
        <?php else: ?>
            <a class="gs-page-btn" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" aria-label="Trang trước">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </a>
        <?php endif; ?>

        <?php $previousRendered = null; ?>
        <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($previousRendered !== null && $page - $previousRendered > 1): ?>
                <span class="gs-page-dots" aria-hidden="true">…</span>
            <?php endif; ?>

            <?php if($page === $current): ?>
                <span class="gs-page-btn is-active" aria-current="page"><?php echo e($page); ?></span>
            <?php else: ?>
                <a class="gs-page-btn" href="<?php echo e($paginator->url($page)); ?>" aria-label="Trang <?php echo e($page); ?>"><?php echo e($page); ?></a>
            <?php endif; ?>

            <?php $previousRendered = $page; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php if($paginator->hasMorePages()): ?>
            <a class="gs-page-btn" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" aria-label="Trang sau">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </a>
        <?php else: ?>
            <span class="gs-page-btn is-disabled" aria-disabled="true" aria-label="Trang sau">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
<?php /**PATH D:\LaravelProjects\GreenShop\resources\views/components/pagination.blade.php ENDPATH**/ ?>