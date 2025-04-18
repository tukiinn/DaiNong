@extends('layouts.app')

@section('content')

    <!-- Banner -->
    <div class="banner-container position-relative mb-4">
        <img src="{{ asset('images/banner/organic-breadcrumb-1.jpg') }}" alt="Banner quảng cáo" class="banner-image w-100" style="height: 130px; object-fit: cover;">
        <div class="banner-overlay position-absolute top-50 start-50 translate-middle text-center">
            <h2 class="text-dark">{{ $newsItem->title }}</h2>
            <nav aria-label="breadcrumb" class="d-flex justify-content-center">
                <ol class="breadcrumb bg-transparent mb-0">
                  <li class="breadcrumb-item">
                    <a href="{{ route('home') }}" class="text-dark">Trang chủ</a>
                  </li>
                  <li class="breadcrumb-item">
                    <a href="{{ route('news.index') }}" class="text-dark">Tin tức</a>
                  </li>
                </ol>
              </nav>
              

        </div>
    </div>

  <!-- News Container -->
  <div class="news-container">
    <h1 class="news-title">{{ $newsItem->title }}</h1>
    <p class="news-date text-center text-muted">
    {{ \Carbon\Carbon::parse($newsItem->created_at)->format('d/m/Y') }}
    </p>

    @if($newsItem->image)
      <div class="news-image">
        <img src="{{ asset($newsItem->image) }}" alt="{{ $newsItem->title }}">
      </div>
    @endif

    <div class="news-content">
      {!! $newsItem->content !!}
    </div>
  </div>
</div>

<!-- CSS -->
<style>

  /* News Container */
  .news-container {
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-top: 30px;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
  }

  /* Tiêu đề bài viết */
.news-title {
  font-size: 2rem;
  font-weight: 700;
  color: #28a745; /* Màu nổi bật hơn */
  text-align: center;
  margin-bottom: 20px;
}

/* Hình ảnh bài viết */
.news-image {
  text-align: center;
  margin-bottom: 20px;
}
.news-image img {
  max-width: 100%;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(255,255,255,0.1);
}

/* Nội dung bài viết */
.news-content {
  font-family: 'Roboto', sans-serif;
  font-size: 1.1rem;
  line-height: 1.8;
  color: #e0e0e0; /* Chữ sáng hơn */
}

/* Tiêu đề trong bài viết */
.news-content h1, 
.news-content h2, 
.news-content h3,
.news-content h4 {
  
  font-weight: bold;
  margin-top: 1.5rem;
  margin-bottom: 0.5rem;
  color: #28a745; /* Màu nổi bật */
}

/* Định dạng đoạn văn */
.news-content p {
  margin-bottom: 1rem;
  text-align: justify;
  color: #2f2f2f; /* Chữ trắng hơn */
}

/* Danh sách */
.news-content ul, 
.news-content ol {
  margin: 1rem 0;
  padding-left: 2rem;
  color: #ddd; /* Màu sáng */
}
.news-content ul {
  list-style: disc;
}
.news-content ol {
  list-style: decimal;
}

/* Liên kết */
.news-content a {
  color: #28a745;
  text-decoration: underline;
  font-weight: 600;
}
.news-content a:hover {
  color: #28a745;
}

/* Trích dẫn */
.news-content blockquote {
  border-left: 4px solid #28a745;
  padding-left: 15px;
  font-style: italic;
  color: #bbb;
  margin: 20px 0;
}

/* Bảng */
.news-content table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
  background: #222;
}
.news-content table td, 
.news-content table th {
  border: 1px solid #444;
  padding: 8px;
  color: #f0f0f0;
}
.news-content table th {
  background: #333;
  font-weight: bold;
}

.news-content img {
  max-width: 100% !important;
  height: auto !important;          /* Giữ tỉ lệ ảnh */
  width: auto !important;           /* Không ép chiều ngang */
  display: block;
  margin: 15px auto;
  border-radius: 8px;
  box-shadow: 0 2px 6px rgba(255,255,255,0.1);
}
.news-date {
  font-size: 0.95rem;
  margin-bottom: 20px;
  color: #6c757d;
}


</style>
@endsection