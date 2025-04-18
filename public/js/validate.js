

document.getElementById('order-form').addEventListener('submit', function(e) {
    // Lấy dữ liệu từ form
    let tenKhachHang = document.getElementById('ten_khach_hang').value.trim();
    let soDienThoai = document.getElementById('so_dien_thoai').value.trim();
    let diaChi = document.getElementById('dia_chi').value.trim();

    // Các biến kiểm tra
    let isValid = true;

    // Reset lỗi cũ
    document.getElementById('error-ten-khach-hang').classList.add('d-none');
    document.getElementById('error-so-dien-thoai').classList.add('d-none');
    document.getElementById('error-dia-chi').classList.add('d-none');

    console.log("Tên khách hàng:", tenKhachHang);
    console.log("Số điện thoại:", soDienThoai);
    console.log("Địa chỉ:", diaChi);

    // Kiểm tra tên khách hàng
    if (tenKhachHang.length < 6 || tenKhachHang.length > 20) {
        document.getElementById('error-ten-khach-hang').classList.remove('d-none');
        isValid = false;
    }

    // Kiểm tra số điện thoại
    if (!/^\d{10}$/.test(soDienThoai)) {
        document.getElementById('error-so-dien-thoai').classList.remove('d-none');
        isValid = false;
    }

    // Kiểm tra địa chỉ
    if (diaChi === '') {
        document.getElementById('error-dia-chi').classList.remove('d-none');
        isValid = false;
    }

    // In ra giá trị isValid để kiểm tra
    console.log("isValid:", isValid);

    // Nếu có lỗi, ngăn gửi form
    if (!isValid) {
        e.preventDefault();
    }
});
