@extends('layouts.admin')

<style>
  /* Tùy chỉnh container tin tức */
  .news-container {
    background-color: #f9f9f9;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-top: 30px;
  }
  
  /* Tiêu đề tin tức */
  .news-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
  }
  
  /* Ảnh tin tức */
  .news-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
  }
  
/* Ảnh trong nội dung bài viết - fix méo ảnh */
.news-content img {
    max-width: 100%;
    height: auto !important;         /* Giữ tỉ lệ ảnh */
    width: auto !important;          /* Không ép chiều ngang */
    display: block;
    margin: 20px auto;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

  
  /* Nút quay lại danh sách tin tức */
  .back-btn {
    display: block;
    margin: 0 auto;
    padding: 10px 20px;
    font-size: 1.125rem;
    border-radius: 5px;
  }
     /* Container */
     .container {
        background-color: #fff;
        border-radius: 8px;
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
        color: #007bff;
    }
    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }

    /* Bảng */
    .table {
        margin-bottom: 0;
    }
</style>

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
<div class="container news-container">
    
    @if($newsItem->image)
        <div class="news-image">
            <img src="{{ asset($newsItem->image) }}" alt="{{ $newsItem->title }}" class="img-fluid">
        </div>
    @endif
    <h1 class="news-title">{{ $newsItem->title }}</h1>


    <div class="news-content">
        {!! $newsItem->content !!}
    </div>

    
</div>
</div>
@endsection
