@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Hiển thị lỗi nếu có --}}
   <!-- Banner & Breadcrumb -->
   <div class="banner-container position-relative mb-4">
    <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
    <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
        <h2 class="text-dark">Chỉnh sửa thông tin cá nhân</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-dark">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('profile.index') }}" class="text-dark">Profile</a></li>
            </ol>
        </nav>
    </div>
</div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Form chỉnh sửa thông tin cá nhân -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4 ">
                <div class="card-body position-relative">
                    @if ($user->avatar)
                    <div id="deleteButtonContainer" class="position-absolute" style="top: 32px; right: 57px; z-index:600; ">
                        <form action="{{ route('profile.deleteAvatar') }}" method="POST"
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa avatar không?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="border-radius: 0px; width: 30px; height: 30px; padding: 0; display: flex; justify-content: center; align-items: center;">
                                x
                            </button>
                            
                        </form>
                    </div>
                @endif
                <form action="{{ route('profile.uploadAvatar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                      <!-- Phần Avatar -->
                      <div class="mb-3">
                        <div class="crop-container mt-3 d-flex justify-content-center align-items-center">
                            <img id="avatarPreview" class="crop-image " src="{{ asset($user->avatar ?? 'images/avatars/avtdf.jpg') }}" alt="Xem trước avatar">
                        </div>
                        <label for="avatar" class="form-label my-2">Ảnh đại diện:</label><br>
                        <input type="file" id="avatarInput" name="avatar" class="form-control" accept="image/*">
                        <input type="hidden" id="croppedImage" name="croppedImage">
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button type="submit" class="btn btn-outline-success">Cập nhật</button>
                        <button type="button" id="cancelCrop" class="btn btn-outline-dark" style="display: none;">Hủy Crop</button>
                    </div>
                </form>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                  @csrf
                  
                  <!-- Họ và tên -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Họ và tên" 
                           value="{{ old('name', $user->name) }}" required>
                    <label for="name">Họ và tên</label>
                  </div>
                  
                          <!-- Email -->
                <div class="form-floating mb-3">
                  <input type="email" class="form-control" id="email" name="email" placeholder="Email" 
                        value="{{ old('email', $user->email) }}" readonly>
                  <label for="email">Email</label>
                </div>

                  
                  <!-- Số điện thoại -->
                  <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Số điện thoại" 
                           value="{{ old('phone', $user->phone) }}">
                    <label for="phone">Số điện thoại</label>
                  </div>
                  
                  <!-- Giới tính (Radio Button không dùng form-floating) -->
                  <div class="mb-3">
                    <label class="form-label">Giới tính:</label>
                    <div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderMale" value="male" 
                               {{ old('gender', $user->gender) == 'male' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genderMale">Nam</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderFemale" value="female" 
                               {{ old('gender', $user->gender) == 'female' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genderFemale">Nữ</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender" id="genderOther" value="other" 
                               {{ old('gender', $user->gender) == 'other' ? 'checked' : '' }}>
                        <label class="form-check-label" for="genderOther">Khác</label>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Ngày sinh -->
                  <div class="form-floating mb-3">
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" placeholder="Ngày sinh" 
                           value="{{ old('date_of_birth', $user->date_of_birth) }}">
                    <label for="date_of_birth">Ngày sinh</label>
                  </div>
                  
                  <button type="submit" class="btn btn-outline-success">Cập nhật thông tin</button>
                </form>
                
                    
                </div>
            </div>
        </div>

 <div class="col-md-5">
  <div class="row">
  <h3 class="mb-3">Địa chỉ giao hàng</h3>

<!-- Form thêm địa chỉ -->
<div class="card mb-4">
  <div class="card-body">
    <form action="{{ route('shipping_addresses.store') }}" method="POST">
      @csrf

      <!-- Loại địa chỉ: Nhà riêng hoặc Văn phòng -->
      <div class="mb-3">
        <label class="form-label">Loại địa chỉ</label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="address_name" id="address_home" value="Nhà riêng" required>
            <label class="form-check-label me-5" for="address_home"><i class="fas fa-home text-primary"></i> Nhà riêng</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="address_name" id="address_office" value="Văn phòng" required>
            <label class="form-check-label" for="address_office"><i class="fas fa-building text-warning"></i> Văn phòng</label>
          </div>
        </div>
      </div>

<div class="row mb-3">
  <div class="col-md-6">
    <div class="form-floating">
      <input type="text" name="name_address" id="name_address" class="form-control" placeholder="Nhập họ và tên" required>
      <label for="name">Họ và tên</label>
    </div>
  </div>
  <div class="col-md-6">
    <div class="form-floating">
      <input type="text" name="phone_address" id="phone_address" class="form-control" placeholder="Nhập số điện thoại" required>
      <label for="phone">Số điện thoại</label>
    </div>
  </div>
</div>
  

      <!-- Chọn địa chỉ -->
      <div class="row mb-3">
        <div class="col-md-4">
          <select id="province" name="province" class="form-select" required>
            <option value="">Chọn Tỉnh/Thành</option>
            <!-- Options được load từ backend hoặc qua AJAX -->
          </select>
        </div>
        <div class="col-md-4">
          <select id="district" name="district" class="form-select" required disabled>
            <option value="">Chọn Quận/Huyện</option>
            <!-- Options load khi tỉnh được chọn -->
          </select>
        </div>
        <div class="col-md-4">
          <select id="ward" name="ward" class="form-select" required disabled>
            <option value="">Chọn Phường/Xã</option>
            <!-- Options load khi quận được chọn -->
          </select>
        </div>
      </div>

      <!-- Địa chỉ chi tiết -->
      <div class="mb-3">
        <label for="detailed_address" class="form-label">Địa Chỉ Chi Tiết</label>
        <input type="text" name="detailed_address" id="detailed_address" class="form-control" placeholder="Số nhà, đường,..." required>
      </div>

      <!-- Input ẩn lưu địa chỉ đã hợp nhất -->
      <input type="hidden" name="full_address" id="full_address" required>

      <button type="submit" class="btn btn-outline-success">Thêm địa chỉ</button>
    </form>
  </div>
</div>


<div class="col-12">
  <!-- Danh sách địa chỉ -->
  <div class="mb-4">
    <h3 class="mb-3">Địa chỉ đã lưu</h3>
    @if ($shippingAddresses->count())
      <div class="row g-4">
        @foreach ($shippingAddresses as $address)
          <div class="col">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <h5 class="card-title">
                  @if($address->address_name === 'Nhà riêng')
                      <i class="fas fa-home text-primary"></i> <!-- Icon nhà riêng -->
                  @elseif($address->address_name === 'Văn phòng')
                      <i class="fas fa-building text-warning"></i> <!-- Icon văn phòng -->
                  @endif
                  {{ $address->address_name }}
              </h5>
              
                <!-- Tên và số điện thoại -->
                <p class="card-text mb-1">
                  {{ $address->name_address }} | {{ $address->phone_address }}
                </p>
                <!-- Thông tin địa chỉ -->
                <p class="card-text text-muted">{{ $address->full_address }}</p>
              </div>
              <div class="card-footer bg-white border-0">
                <div class="d-flex justify-content-end">
                  <!-- Nút mở modal sửa -->
                  <button class="btn btn-outline-success btn-sm edit-btn me-2" 
                   data-bs-toggle="modal" 
                   data-bs-target="#editAddressModal"
                   data-address='@json($address)'
                   data-id="{{ $address->id }}"
                    data-name="{{ $address->name_address }}"
                    data-phone="{{ $address->phone_address }}"
                    data-address_name="{{ $address->address_name }}"
                    data-province="{{ $address->province }}"
                    data-district="{{ $address->district }}"
                   data-ward="{{ $address->ward }}"
                   data-detailed_address="{{ $address->detailed_address }}"
                   data-full_address="{{ $address->full_address }}">
                  Sửa
                </button>
                  <!-- Nút xóa -->
                  <form action="{{ route('shipping-addresses.destroy', $address->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này không?');">
                      Xóa
                    </button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p>Chưa có địa chỉ nào được lưu.</p>
    @endif
  </div>
</div>

<!-- Modal chỉnh sửa địa chỉ -->
<div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editAddressModalLabel">Chỉnh Sửa Địa Chỉ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editAddressForm" method="POST">
          @csrf
          @method('PUT')

          <!-- Họ và Tên -->
          <div class="form-floating mb-3">
            <input type="text" name="name_address" id="edit_name" class="form-control" placeholder="Họ và tên" required>
            <label for="edit_name">Họ và tên</label>
          </div>

          <!-- Số điện thoại -->
          <div class="form-floating mb-3">
            <input type="text" name="phone_address" id="edit_phone" class="form-control" placeholder="Số điện thoại" required>
            <label for="edit_phone">Số điện thoại</label>
          </div>

          <!-- Loại địa chỉ (Nhà riêng / Văn phòng) -->
          <div class="mb-3">
            <label class="form-label">Loại địa chỉ:</label>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="address_name" id="edit_home" value="Nhà riêng">
              <label class="form-check-label" for="edit_home"><i class="fas fa-home text-primary"></i> Nhà riêng</label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="address_name" id="edit_office" value="Văn phòng">
              <label class="form-check-label" for="edit_office">   <i class="fas fa-building text-warning"></i>  Văn phòng</label>
            </div>
          </div>

          <!-- Chọn địa chỉ -->
          <div class="row mb-3">
            <div class="col-md-4">
              <select id="edit_province" name="province" class="form-select" required>
                <option value="">Chọn Tỉnh/Thành</option>
              </select>
            </div>
            <div class="col-md-4">
              <select id="edit_district" name="district" class="form-select" required disabled>
                <option value="">Chọn Quận/Huyện</option>
              </select>
            </div>
            <div class="col-md-4">
              <select id="edit_ward" name="ward" class="form-select" required disabled>
                <option value="">Chọn Phường/Xã</option>
              </select>
            </div>
          </div>

          <!-- Địa chỉ chi tiết -->
          <div class="form-floating mb-3">
            <input type="text" name="detailed_address" id="edit_detailed_address" class="form-control" placeholder="Số nhà, đường,..." required>
            <label for="edit_detailed_address">Địa chỉ chi tiết</label>
          </div>

          <!-- Input ẩn lưu địa chỉ đã hợp nhất -->
          <div class="form-floating mb-3">
            <input type="text" name="full_address" id="edit_full_address" class="form-control" placeholder="Địa chỉ đầy đủ" readonly required>
            <label for="edit_full_address">Địa chỉ đầy đủ</label>
        </div>
        
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-success">Cập nhật</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
  </div>
  </div>

  <!-- Form đổi mật khẩu: chiếm 4 cột -->
  <div class="col-md-3">
    <div class="card shadow-sm mb-4">
      <div class="card-header bg-secondary text-white">
        <i class="fa-solid fa-key"></i> Đổi mật khẩu
      </div>
      <div class="card-body">
        <form action="{{ route('profile.changePassword') }}" method="POST">
          @csrf
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Mật khẩu hiện tại" required>
            <label for="current_password">Mật khẩu hiện tại</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu mới" required>
            <label for="password">Mật khẩu mới</label>
          </div>
          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Xác nhận mật khẩu mới" required>
            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <button type="submit" class="btn btn-outline-success">Đổi mật khẩu</button>
            <a href="{{ route('password.request') }}" class="btn btn-outline-dark">Quên mật khẩu?</a>
          </div>
        </form>
      </div>
    </div>
  </div>
    </div>
</div>

<!-- CropperJS CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
      let provinceData = []; // Lưu danh sách tỉnh/thành

      // Gọi API lấy danh sách tỉnh/thành phố
      fetch('https://provinces.open-api.vn/api/?depth=1')
          .then(response => response.json())
          .then(data => {
              provinceData = data;
              let provinceSelects = document.querySelectorAll('#province, #edit_province');
              provinceSelects.forEach(select => {
                  select.innerHTML = '<option value="">Chọn Tỉnh/Thành</option>';
                  data.forEach(province => {
                      let option = document.createElement('option');
                      option.value = province.code;
                      option.text = province.name;
                      select.add(option);
                  });
              });
          });

      // Hàm tải danh sách quận/huyện
      function loadDistricts(provinceCode, districtSelect, wardSelect, selectedDistrict = null, selectedWard = null) {
          fetch(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`)
              .then(response => response.json())
              .then(data => {
                  districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                  districtSelect.disabled = false;
                  data.districts.forEach(district => {
                      let option = document.createElement('option');
                      option.value = district.code;
                      option.text = district.name;
                      districtSelect.add(option);
                  });

                  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                  wardSelect.disabled = true;

                  if (selectedDistrict) {
                      setTimeout(() => {
                          districtSelect.value = selectedDistrict;
                          districtSelect.dispatchEvent(new Event('change'));

                          if (selectedWard) {
                              setTimeout(() => {
                                  wardSelect.value = selectedWard;
                              }, 500);
                          }
                      }, 500);
                  }
              });
      }

      // Hàm tải danh sách phường/xã
      function loadWards(districtCode, wardSelect, selectedWard = null) {
          fetch(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`)
              .then(response => response.json())
              .then(data => {
                  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                  wardSelect.disabled = false;
                  data.wards.forEach(ward => {
                      let option = document.createElement('option');
                      option.value = ward.code;
                      option.text = ward.name;
                      wardSelect.add(option);
                  });

                  if (selectedWard) {
                      setTimeout(() => {
                          wardSelect.value = selectedWard;
                      }, 500);
                  }
              });
      }

      // Xử lý chọn tỉnh
      document.querySelectorAll('#province, #edit_province').forEach(select => {
          select.addEventListener('change', function () {
              let provinceCode = this.value;
              let districtSelect = this.id === 'province' ? document.getElementById('district') : document.getElementById('edit_district');
              let wardSelect = this.id === 'province' ? document.getElementById('ward') : document.getElementById('edit_ward');

              if (provinceCode) {
                  loadDistricts(provinceCode, districtSelect, wardSelect);
              } else {
                  districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
                  districtSelect.disabled = true;
                  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                  wardSelect.disabled = true;
              }
          });
      });

      // Xử lý chọn quận/huyện
      document.querySelectorAll('#district, #edit_district').forEach(select => {
          select.addEventListener('change', function () {
              let districtCode = this.value;
              let wardSelect = this.id === 'district' ? document.getElementById('ward') : document.getElementById('edit_ward');

              if (districtCode) {
                  loadWards(districtCode, wardSelect);
              } else {
                  wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
                  wardSelect.disabled = true;
              }
          });
      });

      // Khi mở modal sửa địa chỉ
      document.querySelectorAll('.edit-btn').forEach(button => {
          button.addEventListener('click', function () {
            let address = JSON.parse(this.getAttribute('data-address'));
              const id = this.getAttribute('data-id');
              const name = this.getAttribute('data-name');
              const phone = this.getAttribute('data-phone');
              const provinceCode = this.getAttribute('data-province');
              const districtCode = this.getAttribute('data-district');
              const wardCode = this.getAttribute('data-ward');
              const detailedAddress = this.getAttribute('data-detailed_address');
              const addressName =this.getAttribute('data-address_name');
              const fullAddress =this.getAttribute('data-full_address');

              document.getElementById('edit_name').value = name;
              document.getElementById('edit_phone').value = phone;
              document.getElementById('edit_detailed_address').value = detailedAddress;
              document.getElementById('edit_full_address').value = fullAddress;

              // Chọn radio button theo giá trị hiện có
              if (addressName === "Nhà riêng") {
                document.getElementById('edit_home').checked = true;
            } else {
                document.getElementById('edit_office').checked = true;
            }


              let editProvince = document.getElementById('edit_province');
              let editDistrict = document.getElementById('edit_district');
              let editWard = document.getElementById('edit_ward');



            // Cập nhật action cho form với ID của địa chỉ
            let form = document.getElementById('editAddressForm');
            form.action = `/shipping-addresses/${address.id}`;
              // Đảm bảo danh sách tỉnh/thành đã load xong
              if (provinceData.length > 0) {
                  editProvince.value = provinceCode;
                  editProvince.dispatchEvent(new Event('change'));

                  setTimeout(() => {
                      loadDistricts(provinceCode, editDistrict, editWard, districtCode, wardCode);
                  }, 500);
              } else {
                  let checkInterval = setInterval(() => {
                      if (provinceData.length > 0) {
                          editProvince.value = provinceCode;
                          editProvince.dispatchEvent(new Event('change'));

                          setTimeout(() => {
                              loadDistricts(provinceCode, editDistrict, editWard, districtCode, wardCode);
                          }, 500);

                          clearInterval(checkInterval);
                      }
                  }, 500);
              }
          });
      });

      // Theo dõi khi người dùng nhập địa chỉ chi tiết
      document.getElementById('detailed_address').addEventListener('input', updateFullAddress);
      document.getElementById('edit_detailed_address').addEventListener('input', updateFullAddress);

      // Hàm hợp nhất các trường địa chỉ và cập nhật input ẩn
      function updateFullAddress() {
          const provinceSelect = document.getElementById('province') || document.getElementById('edit_province');
          const districtSelect = document.getElementById('district') || document.getElementById('edit_district');
          const wardSelect = document.getElementById('ward') || document.getElementById('edit_ward');
          const detailedAddress = document.getElementById('detailed_address').value.trim() || document.getElementById('edit_detailed_address').value.trim();

          const provinceText = provinceSelect.selectedOptions[0] ? provinceSelect.selectedOptions[0].text : '';
          const districtText = districtSelect.selectedOptions[0] ? districtSelect.selectedOptions[0].text : '';
          const wardText = wardSelect.selectedOptions[0] ? wardSelect.selectedOptions[0].text : '';

          let fullAddress = detailedAddress;
          if (wardText && wardText !== 'Chọn Phường/Xã') {
              fullAddress += ', ' + wardText;
          }
          if (districtText && districtText !== 'Chọn Quận/Huyện') {
              fullAddress += ', ' + districtText;
          }
          if (provinceText && provinceText !== 'Chọn Tỉnh/Thành') {
              fullAddress += ', ' + provinceText;
          }

          document.getElementById('full_address').value = fullAddress;
      }
  });
</script>

    
<script>
    const avatarInput = document.getElementById('avatarInput');
    const avatarPreview = document.getElementById('avatarPreview');
    const croppedImage = document.getElementById('croppedImage');
    const deleteButtonContainer = document.getElementById('deleteButtonContainer');
    const cancelCrop = document.getElementById('cancelCrop');
    let cropper;
    // Lưu lại ảnh cũ ban đầu
    let originalAvatarSrc = avatarPreview.src;

    if (avatarInput) {
        avatarInput.addEventListener('change', (event) => {
            const files = event.target.files;
            if (files && files.length > 0) {
                // Lưu lại ảnh hiện tại trước khi crop
                originalAvatarSrc = avatarPreview.src;

                const file = files[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarPreview.src = e.target.result;
                    if (cropper) {
                        cropper.destroy();
                    }
                    // Ẩn nút xóa avatar khi bắt đầu crop
                    if (deleteButtonContainer) {
                        deleteButtonContainer.style.display = 'none';
                    }
                    // Hiển thị nút Hủy Crop
                    if (cancelCrop) {
                        cancelCrop.style.display = 'block';
                    }
                    cropper = new Cropper(avatarPreview, {
                        aspectRatio: 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        crop() {
                            const canvas = cropper.getCroppedCanvas();
                            croppedImage.value = canvas.toDataURL('image/jpeg', 0.7);
                        }
                    });
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (cancelCrop) {
    cancelCrop.addEventListener('click', () => {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        // Xóa giá trị của input file đã chọn
        if (avatarInput) {
            avatarInput.value = "";
        }
        // Khôi phục lại ảnh cũ
        avatarPreview.src = originalAvatarSrc;
        // Hiển thị lại nút xóa avatar
        if (deleteButtonContainer) {
            deleteButtonContainer.style.display = 'block';
        }
        // Ẩn nút Hủy Crop
        cancelCrop.style.display = 'none';
    });
}

</script>
<style>
    .crop-container {
        width: 100%;
        height: 300px;
        position: relative;
        

    }
    .crop-image {
        max-width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 500;
    }
</style>
@endsection
