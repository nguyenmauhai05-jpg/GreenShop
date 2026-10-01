<div id="deleteCategoryModal" class="category-modal">
    <div class="category-modal-overlay" onclick="closeDeleteCategoryModal()"></div>

    <div class="category-modal-dialog category-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="deleteCategoryTitle">
        <div class="category-delete-icon" aria-hidden="true">!</div>

        <h2 id="deleteCategoryTitle">Xóa danh mục</h2>

        <p>
            Bạn có chắc chắn muốn xóa danh mục
            <strong id="deleteCategoryName"></strong> không?
        </p>

        <div id="deleteCategoryWarning" class="category-delete-warning"></div>

        <form id="deleteCategoryForm" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="category-modal-footer category-delete-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteCategoryModal()">
                    Hủy bỏ
                </button>
                <button id="deleteCategoryButton" type="submit" class="btn-modal-delete">
                    Xóa danh mục
                </button>
            </div>
        </form>
    </div>
</div>
