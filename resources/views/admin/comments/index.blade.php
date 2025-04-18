@extends('layouts.admin')

@section('content')
<div class="container p-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Bình Luận Sản Phẩm</li>
        </ol>
    </nav>

    <h1 class="mb-4 text-center">Quản Lý Bình Luận Sản Phẩm</h1>

    <div class="row">
        @foreach($comments as $comment)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 d-flex flex-column uniform-card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>ID: {{ $comment->id }}</strong></span>
                            <small class="text-muted">{{ $comment->created_at->format('d/m/Y') }}</small>
                        </div>
                        @if($comment->product)
                        <div class="mt-2 d-flex align-items-center">
                            <a href="{{ route('products.show', $comment->product->id) }}" target="_blank" class="d-flex align-items-center text-decoration-none product-link">
                                <img src="{{ asset($comment->product->image ?? 'images/no-image.png') }}" alt="{{ $comment->product->product_name }}" class="img-thumbnail" style="width: 50px; height: 50px;">
                                <span class="ms-2 fw-bold">{{ $comment->product->product_name }}</span>
                            </a>
                        </div>
                    @endif
                    
                    </div>
                    <div class="card-body flex-grow-1">
                        <p class="mb-2"><strong>Người dùng:</strong> {{ $comment->user ? $comment->user->name : 'Ẩn danh' }}</p>
                        <p class="mb-2">
                            <strong>Đánh giá:</strong>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $comment->rating)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                        </p>
                        <p class="mb-2"><strong>Nội dung:</strong> {{ Str::limit($comment->comment, 100) }}</p>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-end mb-2">
                            <form action="{{ route('admin.comments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </form>
                        </div>
                        @if($comment->replies->isNotEmpty())
                            <hr>
                            <h6 class="mb-2">Phản hồi:</h6>
                            <div class="list-group">
                                @foreach($comment->replies as $reply)
                                    <div class="list-group-item list-group-item-action py-2">
                                        <div class="d-flex justify-content-between">
                                            <strong>{{ $reply->user ? $reply->user->name : 'Ẩn danh' }}</strong>
                                            <div>
                                                <small class="text-muted">{{ $reply->created_at->format('d/m/Y') }}</small>
                                                <form action="{{ route('admin.comments.destroy', $reply->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger ms-2">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="mb-0">{{ Str::limit($reply->comment, 80) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-2">
                            <form action="{{ route('product.comments.reply', $comment->id) }}" method="POST">
                                @csrf
                                <div class="input-group input-group-sm">
                                    <input type="text" name="comment" class="form-control" placeholder="Nhập phản hồi..." required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div><!-- End card -->
            </div>
        @endforeach
    </div>
    
    <div class="d-flex justify-content-center">
        {{ $comments->links() }}
    </div>
</div>

<!-- Additional CSS để đồng nhất giao diện -->
<style>
    /* Link đến sản phẩm */
.product-link {
    color: #28a745; /* Màu xanh lá */
    font-weight: bold;
    transition: color 0.3s ease-in-out;
}

.product-link:hover {
    color: #ff9800; /* Màu cam khi hover */
    text-decoration: underline;
}

    /* Container */
    .container {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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

    /* Tiêu đề */
    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: #343a40;
    }

    /* Card Layout */
    .uniform-card {
        min-height: 450px; /* Điều chỉnh chiều cao tối thiểu theo nhu cầu */
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
    .pagination li a, 
    .pagination li span {
        color: #81c784;
        border: 1px solid #81c784;
        padding: 8px 12px;
        border-radius: 4px;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease;
    }
    .pagination li a:hover, 
    .pagination li span:hover {
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
