

<div
    id="deletePlantModal"
    class="plant-delete-modal"
>


    
    <div
        class="plant-delete-backdrop"
        onclick="closeDeletePlantModal()"
    ></div>



    
    <div
        class="plant-delete-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="deletePlantTitle"
    >


        <div class="plant-delete-icon">
            !
        </div>



        <h3 id="deletePlantTitle">
            Xác nhận xóa cây
        </h3>



        <p>

            Bạn có chắc chắn muốn xóa

            <strong id="deletePlantName"></strong>

            không?

        </p>



        <p class="plant-delete-note">

            Nếu cây đã phát sinh dữ liệu liên quan,
            hệ thống sẽ chuyển cây sang trạng thái

            <strong>
                Ẩn
            </strong>

            thay vì xóa vĩnh viễn.

        </p>



        <form
            id="deletePlantForm"
            method="POST"
            action=""
        >

            <?php echo csrf_field(); ?>

            <?php echo method_field('DELETE'); ?>



            <div class="plant-delete-actions">


                <button
                    type="button"
                    class="plant-delete-cancel"
                    onclick="closeDeletePlantModal()"
                >
                    Hủy bỏ
                </button>



                <button
                    type="submit"
                    class="plant-delete-confirm"
                >
                    Xóa cây
                </button>


            </div>


        </form>


    </div>


</div>
<?php /**PATH C:\xampp\htdocs\GreenShop\resources\views/admin/cay_canh/components/delete-modal.blade.php ENDPATH**/ ?>