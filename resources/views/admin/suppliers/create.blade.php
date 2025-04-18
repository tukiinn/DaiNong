@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Thêm Nhà Cung Cấp Mới</h1>
    <form action="{{ route('admin.suppliers.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="supplier_name" class="form-label">Tên Nhà Cung Cấp</label>
            <input type="text" class="form-control @error('supplier_name') is-invalid @enderror" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required>
            @error('supplier_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="contact_info" class="form-label">Thông Tin Liên Hệ</label>
            <input type="text" class="form-control @error('contact_info') is-invalid @enderror" id="contact_info" name="contact_info" value="{{ old('contact_info') }}">
            @error('contact_info')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Địa Chỉ</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Thêm Nhà Cung Cấp</button>
    </form>
</div>
@endsection
