@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Tin Tức</li>
        </ol>
    </nav>
    <!-- End Breadcrumb -->

    <!-- Tiêu đề trang -->
    <h1 class="mb-4 text-center">Quản lý Tin Tức</h1>

    <!-- Nút Thêm bài viết -->
    <div class="mb-3 text-center">
        <a href="{{ route('admin.news.create') }}" class="btn btn-add">
            <i class="fas fa-plus"></i> Thêm bài viết
        </a>
    </div>

    <!-- Bảng danh sách tin tức -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered modern-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tiêu đề</th>
                    <th>Slug</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($news as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item->title }}</td>
                        <td>{{ $item->slug }}</td>
                        <td>
                            <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-icon btn-edit" title="Sửa">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.news.show', $item->slug) }}" class="btn btn-icon btn-view" title="Xem">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.news.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-icon btn-delete" title="Xóa">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <div class="d-flex justify-content-center mt-3">
        {{ $news->links() }}
    </div>
</div>

<!-- Inline CSS Styles -->
<style>
    /* Container */
    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Breadcrumb */
    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 20px;
        font-size: 0.9rem;
    }
    .breadcrumb-item a {
        text-decoration: none;
        color: #81c784;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Tiêu đề trang */
    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }

    /* Nút Thêm bài viết */
    .btn-add {
        background-color: #81c784;
        border: none;
        color: #fff;
        padding: 10px 20px;
        font-size: 1rem;
        border-radius: 50px;
        transition: background-color 0.3s ease, transform 0.3s ease;
        text-decoration: none;
    }
    .btn-add:hover {
        background-color: #689f65;
        transform: translateY(-2px);
    }

    /* Bảng tin tức hiện đại */
    .modern-table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    .modern-table thead {
        background-color: #81c784;
        color: #fff;
    }
    .modern-table th,
    .modern-table td {
        vertical-align: middle;
        text-align: center;
        padding: 15px;
    }
    .modern-table tbody tr:nth-of-type(odd) {
        background-color: rgba(129, 199, 132, 0.1);
    }
    .modern-table tbody tr:hover {
        background-color: rgba(104, 159, 101, 0.15);
        cursor: pointer;
    }

    /* Nút hành động dạng icon */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 50%;
        color: #fff;
        margin: 0 2px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }
    .btn-icon:hover {
        transform: scale(1.1);
    }
    /* Sửa: dùng màu vàng nhạt */
    .btn-edit {
        background-color: #ffc107;
    }
    .btn-edit:hover {
        background-color: #e0a800;
    }
    /* Xem: dùng màu info */
    .btn-view {
        background-color: #17a2b8;
    }
    .btn-view:hover {
        background-color: #138496;
    }
    /* Xóa: dùng màu danger */
    .btn-delete {
        background-color: #dc3545;
    }
    .btn-delete:hover {
        background-color: #c82333;
    }

    /* Phân trang */
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding-left: 0;
        margin-top: 20px;
    }
    .pagination li {
        margin: 0 5px;
    }
    .pagination li a, .pagination li span {
        color: #81c784;
        border: 1px solid #81c784;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .pagination li a:hover, .pagination li span:hover {
        background-color: #81c784;
        color: #fff;
    }
    .pagination li.active span {
        background-color: #81c784;
        color: #fff;
        border-color: #81c784;
    }
</style>
@endsection
