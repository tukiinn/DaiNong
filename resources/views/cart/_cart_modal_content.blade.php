@if($cartItems && count($cartItems) > 0)
  <ul class="list-group cart-items-list">
    @foreach($cartItems as $item)
      @php
          // Xác định giá cơ sở: nếu discount_price > 0 thì ưu tiên dùng nó, ngược lại dùng price
          $basePrice = (!empty($item['discount_price']) && $item['discount_price'] > 0)
                        ? $item['discount_price']
                        : $item['price'];
          // Ban đầu, giá áp dụng là giá cơ sở
          $price = $basePrice;
          // Nếu có thông tin size, tính giá theo các mức quy đổi (nếu có giá cho 500g hoặc 250g)
          if (isset($item['size'])) {
              if ($item['size'] == '500g') {
                  $price = $item['price_500g'] ?? $basePrice;
              } elseif ($item['size'] == '250g') {
                  $price = $item['price_250g'] ?? $basePrice;
              }
          }
      @endphp
      <li class="list-group-item cart-item shadow-sm my-2">
        <button class="btn cart-item-remove" data-product-id="{{ $item['product_id'] }}" data-size="{{ $item['size'] ?? '' }}">&times;</button>
        <div class="cart-item-wrapper cart-item_">
          <img src="{{ asset($item['image'] ?? 'https://placehold.it/100x100') }}" alt="{{ $item['name'] }}" class="img-fluid cart-item-image" style="width: 80px; height: 80px; object-fit: cover">
          <div class="cart-item-details">
            <a href="{{ route('products.show', $item['product_id']) }}" class="text-decoration-none py-1 cart-item-name">
              {{ $item['name'] }}
            </a>
            @if(isset($item['unit']) && $item['unit'] === 'kg' && isset($item['size']))
              <small class="text-muted">
                <select class="form-select form-select-sm update-size" data-product-id="{{ $item['product_id'] }}" data-old-size="{{ $item['size'] }}">
                  <option value="1kg" {{ $item['size'] == '1kg' ? 'selected' : '' }}>1kg</option>
                  <option value="500g" {{ $item['size'] == '500g' ? 'selected' : '' }}>500g</option>
                  <option value="250g" {{ $item['size'] == '250g' ? 'selected' : '' }}>250g</option>
                </select>
              </small>
            @endif

            <div class="cart-item-quantity d-flex align-items-center justify-content-start mt-1">
              <input type="number" class="form-control form-control-sm update-quantity" data-product-id="{{ $item['product_id'] }}" value="{{ $item['so_luong'] }}" min="1">
              <span class="ms-2">×</span>
              <span class="price ms-1">{{ number_format($price, 0, ',', '.') }}₫</span>
              <span class="ms-2"> = </span>
              <span class="total-price ms-1">{{ number_format($item['so_luong'] * $price, 0, ',', '.') }}₫</span>
            </div>
          </div>
        </div>
        <button class="btn btn-primary btn-sm update-cart-item ms-3" data-product-id="{{ $item['product_id'] }}">
          <i class="fa-solid fa-arrows-rotate"></i>
        </button>
      </li>
    @endforeach
  </ul>
@else
  <p class="text-center">Giỏ hàng của bạn trống!</p>
@endif




<style>


  .cart-items-list {
    list-style: none;
    padding: 0;

  }

  .cart-item {
    position: relative;
    display: flex;
    align-items: center;
    padding: 10px;
  border: 1px solid #eee;
  border-radius: 5px;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .cart-item_ {
    position: relative;
    display: flex;
    align-items: center;
  }

.cart-item:hover{
  transform: scale(1.02);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

  .cart-item-image {
    margin-right: 10px;
  }

  .cart-item-details {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }

  .cart-item-name {
    font-size: 1rem;
    font-weight: bold;
    transition: color 0.3s ease;
    color: black;
    
    width: 200px;           /* Hoặc sử dụng max-width: 200px; */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

  .cart-item-name:hover {
    color:#28a745;

  }

  .cart-item-quantity {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    width: 100%;
  }

.total-price{
  font-size: 1rem;
  font-weight: bold;
}
  .update-quantity {
    width: 45px;
  }

  .update-size {
    width: 76px;
  }

/* Nút xóa ở góc trên bên trái */
.cart-item-remove {
  position: absolute;
  top: 5px;   /* Khoảng cách từ mép trên, có thể điều chỉnh */
  right: 5px;  /* Khoảng cách từ mép trái, có thể điều chỉnh */
  background: transparent;
  border: none;
  font-size: 1.5rem;
  color: #dc3545;
  cursor: pointer;
}

/* Nút cập nhật ở góc dưới bên phải */
.update-cart-item {
  position: absolute;
  bottom: 10px;  /* Khoảng cách từ mép dưới, có thể điều chỉnh */
  right: 10px;   /* Khoảng cách từ mép phải, có thể điều chỉnh */
  background-color: #28a745;
    border: none;

}

  /* Các lớp tiện ích margin của Bootstrap */
  .ms-1 { margin-left: 0.25rem !important; }
  .ms-2 { margin-left: 0.5rem !important; }
  .ms-3 { margin-left: 1rem !important; }
  .ms-5 { margin-left: 3rem !important; }


  @media (max-width: 767px) {
    .cart-item {
      flex-direction: column;
      align-items: flex-start;
    }
    .cart-item-details {
      margin-top: 10px;
    }
    .cart-item-quantity {
      justify-content: flex-start;
      margin-top: 10px;
    }
  }
</style>
