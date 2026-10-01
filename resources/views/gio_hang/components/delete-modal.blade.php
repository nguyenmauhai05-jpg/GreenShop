<div
    class="cart-modal"
    id="deleteCartModal"
>

    <div class="cart-modal-dialog">

        <div class="delete-modal-icon">
            🗑
        </div>


        <h3>
            Xóa sản phẩm khỏi giỏ hàng?
        </h3>


        <p>
            Bạn có chắc chắn muốn xóa sản phẩm này
            khỏi giỏ hàng?
        </p>


        <div class="cart-modal-actions">

            <button
                type="button"
                class="modal-cancel-button"
                data-close-delete-modal
            >
                Hủy
            </button>


            <form
                method="POST"
                action=""
                id="deleteCartForm"
            >

                @csrf
                @method('DELETE')


                <button
                    type="submit"
                    class="modal-delete-button"
                >
                    Xóa
                </button>

            </form>

        </div>

    </div>

</div>