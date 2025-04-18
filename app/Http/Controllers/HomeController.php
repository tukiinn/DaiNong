<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\News; // Import model News nếu bạn có

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        $bestSellingProducts = $products->sortByDesc(function ($product) {
            return $product->orderItems->sum('so_luong');
        })->take(8);

      // Lấy sản phẩm khuyến mại (có giảm giá hợp lệ, giá giảm không được bằng 0)
$promotionalProducts = Product::whereNotNull('discount_price')
->whereColumn('discount_price', '<', 'price') // Giá giảm phải nhỏ hơn giá gốc
->where('discount_price', '>', 0) // Loại bỏ sản phẩm có discount_price = 0
->where('price', '>', 0) // Đảm bảo giá trị ban đầu > 0
->orderByRaw('((price - discount_price) / price) DESC') // Sắp xếp theo % giảm giá giảm dần
->take(8)
->get();


        // Lấy 8 bài viết mới (news)
        $news = News::latest()->take(4)->get();

        return view('home', compact('products', 'bestSellingProducts', 'promotionalProducts', 'news'));
    }
}
