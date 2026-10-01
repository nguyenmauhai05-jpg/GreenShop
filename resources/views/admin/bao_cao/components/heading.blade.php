<div class="report-heading report-heading-actions-only">

    <div class="report-heading-actions">

        <a
            href="{{ route(
                'admin.bao-cao.export-excel',
                request()->query()
            ) }}"
            class="report-export-btn"
        >
            ↓ Xuất Excel
        </a>


        <a
            href="{{ route(
                'admin.bao-cao.export-pdf',
                request()->query()
            ) }}"
            class="report-export-btn"
        >
            ↓ Xuất PDF
        </a>

    </div>

</div>