<div class="report-heading report-heading-actions-only">

    <div class="report-heading-actions">

        <a
            href="<?php echo e(route(
                'admin.bao-cao.export-excel',
                request()->query()
            )); ?>"
            class="report-export-btn"
        >
            ↓ Xuất Excel
        </a>


        <a
            href="<?php echo e(route(
                'admin.bao-cao.export-pdf',
                request()->query()
            )); ?>"
            class="report-export-btn"
        >
            ↓ Xuất PDF
        </a>

    </div>

</div><?php /**PATH D:\LaravelProjects\GreenShop\resources\views/admin/bao_cao/components/heading.blade.php ENDPATH**/ ?>