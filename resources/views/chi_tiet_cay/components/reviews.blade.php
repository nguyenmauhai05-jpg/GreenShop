<div class="reviews">


<h2>
Đánh giá khách hàng
</h2>



@forelse($cay->danhGias as $item)


<div>


⭐ {{$item->so_sao}}


<p>
{{$item->noi_dung}}
</p>

@if(filled($item->phan_hoi_admin))
    <div class="review-admin-reply">
        <strong>GreenShop phản hồi:</strong>
        <p>{{ $item->phan_hoi_admin }}</p>
        @if($item->phan_hoi_luc)
            <small>{{ $item->phan_hoi_luc->format('d/m/Y H:i') }}</small>
        @endif
    </div>
@endif


</div>



@empty


<p>
Sản phẩm chưa có đánh giá.
</p>



@endforelse


</div>