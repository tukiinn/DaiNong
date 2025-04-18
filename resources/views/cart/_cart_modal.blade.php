<!-- Modal Giỏ Hàng - Modal mở từ bên phải -->
<div class="modal right fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" inert>
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Header của modal -->
            <div class="modal-header">
                <h5 class="modal-title text-dark id="cartModalLabel">Giỏ hàng của bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <!-- Nội dung giỏ hàng: Đây là nơi load AJAX -->
            <div class="modal-body" id="cartModalContent">
                <!-- Nội dung sẽ được load qua AJAX khi modal mở -->
                <div class="text-center">
                    <span>Đang tải dữ liệu...</span>
                </div>
            </div>
            <!-- Footer với nút chi tiết giỏ hàng và nút thanh toán -->
            <div class="modal-footer justify-content-center">
                 @if(auth()->check())
                    <a href="{{ route('cart.checkout') }}" class="btn btn-success">Thanh toán</a>
                @else
                    <button type="button" class="btn btn-success" onclick="requireLogin()">Thanh toán</button>
                @endif
                <a href="{{ route('cart.index') }}" class="btn btn-secondary">Chi tiết giỏ hàng</a>
               
            </div>
        </div>
    </div>
</div>

<!-- Hàm requireLogin() để yêu cầu đăng nhập khi người dùng chưa đăng nhập -->
<script>
    function requireLogin() {
        Swal.fire({
            icon: 'warning',
            title: 'Bạn chưa đăng nhập!',
            text: 'Vui lòng đăng nhập để tiếp tục thanh toán.',
            showCancelButton: true,
            confirmButtonText: 'Đăng nhập ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('login') }}";
            }
        });
    }
</script>

<!-- CSS để modal trượt từ bên phải -->
<style>
    
.modal.right .modal-dialog {
    position: fixed;
    margin: 0; /* Loại bỏ margin gây ảnh hưởng */
    width: 420px;
    height: 100%;
    right: 0;
    top: 0;
    transform: translateX(100%);
    transition: transform 0.3s ease-out;
    z-index: 9999; /* Đảm bảo modal luôn trên cùng */
}

.modal.right.fade.show .modal-dialog {
    transform: translateX(0);
}

.modal.right .modal-content {
    height: 100%;
    border: none;
    border-radius: 0;
    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.2); /* Thêm hiệu ứng đổ bóng nhẹ */
}



.fade-out {
    transition: transform 0.5s ease, opacity 0.5s ease;
    transform: translateX(100%);
    opacity: 0;
}

.cart-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;

  z-index: 10;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 4px; /* nếu cần bo góc */
}




</style>
  
<script>
    document.addEventListener('DOMContentLoaded', function () {
      // Hàm cập nhật sản phẩm trong giỏ hàng, xử lý cả trường hợp không có size (chỉ số lượng)
      function updateCartItem(productId, newSize, quantity, oldSize) {
  quantity = Number(quantity) || 1;
  const payload = { quantity: quantity };

  if (newSize && newSize.trim() !== '') {
    payload.size = newSize.trim();
  }
  if (oldSize && oldSize.trim() !== '') {
    payload.old_size = oldSize.trim();
  }

  // Hiển thị overlay loading ngay trên giỏ hàng (có thể là một lớp CSS làm mờ nội dung)
  const cartModalContent = document.getElementById('cartModalContent');
  const overlay = document.createElement('div');
  overlay.className = 'cart-loading-overlay';
  overlay.innerHTML = `
    <div class="spinner-border text-light" role="status">
      <span class="visually-hidden">Đang cập nhật...</span>
    </div>
  `;
  cartModalContent.appendChild(overlay);

  fetch(`/cart/update/${productId}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(response => response.json())
  .then(data => {
    // Sau khi cập nhật, loại bỏ overlay và load lại giỏ hàng với hiệu ứng
    overlay.remove();
    if (data.success) {
      // Hiệu ứng fade nhẹ để báo hiệu đã cập nhật thành công
      $(cartModalContent).fadeOut(200, function(){
         loadCartContent();
         $(cartModalContent).fadeIn(200);
      });
    } else {
      alert(data.message || 'Cập nhật sản phẩm không thành công!');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    overlay.remove();
    alert('Cập nhật sản phẩm không thành công!');
  });
}

    
      // Gắn sự kiện cho nút cập nhật giỏ hàng và tự động cập nhật khi thay đổi size hoặc số lượng
      function attachUpdateEvents() {
        // Sự kiện cho nút cập nhật (nếu người dùng muốn cập nhật thủ công)
        const updateButtons = document.querySelectorAll('.update-cart-item');
        updateButtons.forEach(function(button) {
          button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const sizeSelector = document.querySelector(`.update-size[data-product-id='${productId}']`);
            const newSize = sizeSelector ? sizeSelector.value : null;
            const oldSize = sizeSelector ? sizeSelector.getAttribute('data-old-size') : null;
            const quantityInput = document.querySelector(`.update-quantity[data-product-id='${productId}']`);
            const newQuantity = quantityInput ? parseInt(quantityInput.value) : 1;
            updateCartItem(productId, newSize, newQuantity, oldSize);
          });
        });
    
        // Tự động cập nhật khi thay đổi size
        const updateSizeElements = document.querySelectorAll('.update-size');
        updateSizeElements.forEach(function(element) {
          element.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const newSize = this.value;
            const oldSize = this.getAttribute('data-old-size');
            const quantityInput = document.querySelector(`.update-quantity[data-product-id='${productId}']`);
            const newQuantity = quantityInput ? parseInt(quantityInput.value) : 1;
            updateCartItem(productId, newSize, newQuantity, oldSize);
          });
        });
    
        // Tự động cập nhật khi thay đổi số lượng
        const updateQuantityElements = document.querySelectorAll('.update-quantity');
        updateQuantityElements.forEach(function(element) {
          element.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const newQuantity = parseInt(this.value);
            const sizeSelector = document.querySelector(`.update-size[data-product-id='${productId}']`);
            const newSize = sizeSelector ? sizeSelector.value : null;
            const oldSize = sizeSelector ? sizeSelector.getAttribute('data-old-size') : null;
            updateCartItem(productId, newSize, newQuantity, oldSize);
          });
        });
      }
        
      var cartModal = document.getElementById('cartModal');
        
      cartModal.addEventListener('show.bs.modal', function () {
        cartModal.removeAttribute('inert');
        loadCartContent();
      });
        
      cartModal.addEventListener('hide.bs.modal', function () {
        cartModal.setAttribute('inert', '');
      });
        

      function loadCartContent() {
  const cartModalContent = document.getElementById('cartModalContent');
  
  // Hiển thị spinner loading
  cartModalContent.innerHTML = `
    <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Đang tải...</span>
      </div>
    </div>
  `;
  
  fetch("{{ route('cart.ajax') }}")
    .then(response => response.text())
    .then(html => {
      // Dùng hiệu ứng fade-out trước khi thay nội dung
      $(cartModalContent).fadeOut(200, function(){
         cartModalContent.innerHTML = html;
         // Sau khi đã thay đổi nội dung, fade in để hiển thị mượt mà
         $(cartModalContent).fadeIn(200);
         attachRemoveEvent();
         attachUpdateEvents(); // Gắn lại sự kiện cập nhật size và số lượng
      });
    })
    .catch(error => {
      console.error('Error loading cart data:', error);
      cartModalContent.innerHTML = '<p class="text-center text-danger">Không thể tải dữ liệu giỏ hàng!</p>';
    });
}

        
      function attachRemoveEvent() {
        var removeButtons = document.querySelectorAll('.cart-item-remove');
        removeButtons.forEach(function(button) {
          button.addEventListener('click', function() {
            var productId = this.getAttribute('data-product-id');
            // Lấy size từ attribute data-size (nếu có)
            var size = this.getAttribute('data-size') || '';
            var cartItem = this.closest('.cart-item');
    
            if (cartItem) {
              // Thêm lớp fade-out để tạo hiệu ứng trượt sang trái và biến mất
              cartItem.classList.add('fade-out');
    
              // Sau khi hiệu ứng kết thúc, xóa sản phẩm trực tiếp khỏi DOM
              cartItem.addEventListener('transitionend', function() {
                fetch(`/cart/remove/${productId}`, {
                  method: 'DELETE',
                  headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                  },
                  // Gửi thông tin size qua body
                  body: JSON.stringify({ size: size })
                })
                .then(response => {
                  if (response.ok) {
                    return response.json();
                  } else {
                    throw new Error('Xảy ra lỗi khi gỡ bỏ sản phẩm.');
                  }
                })
                .then(data => {
                  if (data.success) {
                    cartItem.remove();
                    updateCartItemCount(); // Cập nhật số lượng sản phẩm trong giỏ hàng
    
                    // Kiểm tra nếu giỏ hàng trống
                    if (document.querySelectorAll('.cart-item').length === 0) {
                      var modal = document.getElementById('cartModal');
                      var modalInstance = bootstrap.Modal.getInstance(modal); // Sử dụng Bootstrap 5
                      if (modalInstance) {
                        modalInstance.hide(); // Đóng modal
                      }
                    }
                  } else {
                    alert('Xóa sản phẩm không thành công!');
                  }
                })
                .catch(error => {
                  console.error('Error:', error);
                  alert('Xóa sản phẩm không thành công!');
                });
              }, { once: true }); // Đảm bảo sự kiện chỉ chạy một lần
            } else {
              console.error('Error: .cart-item-wrapper không tồn tại.');
              alert('Xảy ra lỗi khi gỡ bỏ sản phẩm. Vui lòng thử lại.');
            }
          });
        });
      }
      
      // Gọi hàm attachRemoveEvent để gán sự kiện khi DOM đã tải
      document.addEventListener('DOMContentLoaded', attachRemoveEvent);
    
      function updateCartItemCount() {
        fetch("{{ route('cart.count') }}")
          .then(response => response.json())
          .then(data => {
            document.getElementById('cartItemCount').innerText = data.totalItems;
          })
          .catch(error => {
            console.error('Error updating cart item count:', error);
          });
      }
        
      // Hàm yêu cầu đăng nhập
      window.requireLogin = function() {
        Swal.fire({
          icon: 'warning',
          title: 'Bạn chưa đăng nhập!',
          text: 'Vui lòng đăng nhập để tiếp tục thanh toán.',
          showCancelButton: true,
          confirmButtonText: 'Đăng nhập ngay',
          cancelButtonText: 'Hủy'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = "{{ route('login') }}";
          }
        });
      }
        
      function addToCart(productId, size, quantity) {
  fetch(`/cart/add/${productId}`, {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ 
      size: size,
      quantity: quantity  // truyền số lượng vào body
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Cập nhật nội dung giỏ hàng
      document.getElementById('cartModalContent').innerHTML = data.cartHtml;
      attachRemoveEvent(); // Gắn lại sự kiện xóa sản phẩm
      attachUpdateEvents(); // Gắn sự kiện cập nhật size và số lượng
      updateCartItemCount(); // Cập nhật số lượng sản phẩm trong giỏ hàng

      // Mở mini cart trực tiếp
      var cartModal = document.getElementById('cartModal');
      var cartModalInstance = new bootstrap.Modal(cartModal);
      cartModalInstance.show();

      // Sau khi modal mở, xóa padding-right của body để tránh layout bị lệch
      setTimeout(() => {
        document.body.style.paddingRight = '0px';
      }, 50);
    } else {
      // Thông báo lỗi bằng alert
      alert('Lỗi: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Đã xảy ra lỗi khi thêm sản phẩm vào giỏ hàng.');
  });
}
        
      // Gắn sự kiện cho nút thêm vào giỏ hàng
      var addToCartButtons = document.querySelectorAll('.add-to-cart');
      addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function() {
          var form = this.closest('.add-to-cart-form');
          var productId = form.getAttribute('data-product-id');
          var size = form.querySelector('input[name="size"]').value;
          var quantity = form.querySelector('input[name="quantity"]').value; // lấy số lượng từ input
          addToCart(productId, size, quantity);
        });
      });
    });
    </script>
    
    