document.addEventListener('DOMContentLoaded', function() {
    // Lắng nghe thay đổi của checkbox để chuyển đổi dark mode cho toàn trang
    const toggle = document.getElementById('toggle');
    toggle.addEventListener('change', function() {
      // Khi checkbox được tích, thêm class 'dark-mode' vào body; nếu bỏ tích thì xóa class đi
      document.body.classList.toggle('dark-mode', this.checked);
    });
  });
  