<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function index()
    {
        $categories = Category::orderBy('sort_order', 'asc')->get();
        return view('categories.index', compact('categories'));
    }
    public function show($id)
{
    $category = Category::findOrFail($id);
    
    // Lấy sản phẩm liên quan, ví dụ: 10 sản phẩm mới nhất
    $relatedProducts = $category->products()->latest()->take(10)->get();
    
    // Lấy các đánh giá của danh mục, sắp xếp theo mới nhất
    $reviews = $category->reviews()->latest()->get();

    // Truyền biến $category, $relatedProducts, $reviews vào view
    return view('categories.show', compact('category', 'relatedProducts', 'reviews'));
}


}
