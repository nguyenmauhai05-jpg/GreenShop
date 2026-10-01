<div class="cart-toast-wrapper">

    @if(session('success'))

        <div class="cart-toast success">
            <span>✓</span>

            <span>
                {{ session('success') }}
            </span>
        </div>

    @endif


    @if(session('warning'))

        <div class="cart-toast warning">
            <span>!</span>

            <span>
                {{ session('warning') }}
            </span>
        </div>

    @endif


    @if(session('error'))

        <div class="cart-toast error">
            <span>×</span>

            <span>
                {{ session('error') }}
            </span>
        </div>

    @endif

</div>