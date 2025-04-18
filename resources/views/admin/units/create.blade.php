@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Thêm Đơn Vị Mới</h1>
    <form action="{{ route('admin.units.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="unit_name" class="form-label">Tên Đơn Vị</label>
            <input type="text" class="form-control @error('unit_name') is-invalid @enderror" id="unit_name" name="unit_name" value="{{ old('unit_name') }}" required>
            @error('unit_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Thêm Đơn Vị</button>
    </form>
</div>
@endsection
