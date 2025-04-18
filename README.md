# 🥬 Đại Nông – Website thương mại điện tử nông sản

Dự án xây dựng website bán hàng nông sản (rau củ, trái cây, ngũ cốc...) với đầy đủ tính năng thực tế dành cho người dùng và quản trị viên. Hướng đến trải nghiệm mua sắm **thân thiện – an toàn – dễ quản lý**.

## 🚀 Tính năng nổi bật

- **Xác thực & phân quyền**: Đăng ký/đăng nhập tích hợp Google, Facebook; phân quyền chi tiết Admin & Khách hàng.
- **Giỏ hàng linh hoạt**: Quản lý giỏ hàng bằng cả Session & Database, tối ưu cho trải nghiệm người dùng.
- **Thanh toán đa cổng**: Tích hợp VNPay, MoMo, PayPal với callback và cập nhật trạng thái đơn hàng real-time.
- **Chatbot AI**: Dialogflow tự động trả lời câu hỏi thường gặp (FAQs).
- **Chat trực tiếp**: Khách hàng có thể nhắn trực tiếp với admin qua WebSocket (Node.js + Laravel Broadcasting).
- **Mini‑game**: Vòng quay may mắn tặng mã giảm giá ngẫu nhiên, tăng tương tác mua sắm.
- **API địa chỉ Việt Nam**: Hỗ trợ chọn tỉnh/thành – quận/huyện chính xác khi đặt hàng.
- **Quản lý toàn diện**: Hệ thống quản trị sản phẩm, tồn kho, đơn hàng, nhân viên, mã giảm giá, đánh giá bình luận.
- **Thống kê & báo cáo**: Dashboard theo dõi doanh thu, sản phẩm bán chạy, hỗ trợ in hóa đơn.
- **Xuất Excel**: Xuất báo cáo doanh thu và dữ liệu quản trị nhanh chóng.

## 🛠️ Công nghệ sử dụng

- **Backend**: Laravel 11, MySQL
- **Frontend**: Bootstrap 5, Font Awesome
- **Realtime**: Laravel Broadcasting + Node.js + WebSocket
- **Thanh toán**: VNPay, MoMo, PayPal
- **Khác**: Dialogflow chatbot, API địa chỉ Việt Nam, ChartJS, export Excel

## 📸 Giao diện Demo

### 👥 Giao diện người dùng (Home)

| Trang chủ | Trang sản phẩm | Giỏ hàng |
|----------|----------------|----------|
| ![Home](https://github.com/user-attachments/assets/c8f5e70f-3988-447f-9eb2-b1b3bb86d09b) | ![Product](https://github.com/user-attachments/assets/1f1f1387-576e-47a4-9b95-4654ad631831) | ![Cart](https://github.com/user-attachments/assets/8a7da2e2-0e1a-4ee4-a2b2-40cde9864acc) |

### 🛠️ Giao diện quản trị (Admin)

| Tổng quan dashboard | Quản lý đơn hàng | Giao diện form quản lý |
|---------------------|------------------|-------------------------|
| ![Dashboard](https://github.com/user-attachments/assets/89b943cc-1e16-410b-9618-67e38e463ccb) | ![Orders](https://github.com/user-attachments/assets/22609908-a000-4638-a08a-11eac72c72ee) | ![Form](https://github.com/user-attachments/assets/d61c254d-909e-4de9-a035-9bfdee02bfa4) |

### 🎮 Tính năng đặc biệt

| Mini game – Vòng quay may mắn |
|-------------------------------|
| ![Lucky spin](https://github.com/user-attachments/assets/c0027f94-e45d-40f3-a9c4-defe7e41fad4) |
