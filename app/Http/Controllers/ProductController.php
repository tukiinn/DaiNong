<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;




class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
    
        // Lọc theo danh mục
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
    
        // Lọc theo tình trạng sản phẩm
        if ($request->filled('condition')) {
            switch ($request->condition) {
                case 'sale':
                    // Chỉ lấy sản phẩm có discount_price > 0 (giá giảm thực sự)
                    $query->where('discount_price', '>', 0)
                          ->where('price', '>', 0);
                    break;
                case 'featured':
                    $query->where('featured', 1);
                    break;
                case 'in-stock':
                    $query->where('stock_quantity', '>', 0);
                    break;
            }
        }
    
        // Lọc theo khoảng giá
        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = (float) $request->min_price;
            $maxPrice = (float) $request->max_price;
            $query->whereBetween(DB::raw('IFNULL(discount_price, price)'), [$minPrice, $maxPrice]);
        }
    
    // Sắp xếp linh hoạt
if ($request->filled('sort')) {
    switch ($request->sort) {
        case 'featured':
            $query->orderBy('featured', 'desc');
            break;
        case 'newest':
            $query->orderBy('created_at', 'desc');
            break;
        case 'price_asc':
            // Nếu discount_price > 0 thì dùng discount_price, ngược lại dùng price
            $query->orderByRaw("IF(discount_price > 0, discount_price, price) ASC");
            break;
        case 'price_desc':
            $query->orderByRaw("IF(discount_price > 0, discount_price, price) DESC");
            break;
        default:
            $query->orderBy('id', 'desc'); // Mặc định
            break;
    }
} else {
    $query->orderBy('id', 'desc'); // Sắp xếp mặc định
}

    
        // Lấy danh sách sản phẩm với phân trang
        $products = $query->paginate(12);
    
        // Lấy danh sách danh mục để hiển thị trong bộ lọc kèm theo số lượng sản phẩm của từng danh mục
        $categories = Category::withCount(['products as product_count'])->get();
    
        return view('products.index', compact('products', 'categories'));
    }
    

    

    public function show($id)
    {
        // Lấy sản phẩm theo id kèm theo quan hệ 'comments.user' để tải luôn bình luận và thông tin người dùng của mỗi bình luận
        $product = Product::with('comments.user')->findOrFail($id);
        $category = $product->category;
    
        if (!$category) {
            abort(404);
        }
    
        // Lấy danh sách sản phẩm liên quan trong cùng danh mục
        $relatedProducts = $category->products()->latest()->take(10)->get();
    
        // Xử lý lưu trữ sản phẩm vừa xem trong session
        $recentlyViewed = session()->get('recently_viewed', []);
        if (($key = array_search($product->id, $recentlyViewed)) !== false) {
            unset($recentlyViewed[$key]);
        }
        array_unshift($recentlyViewed, $product->id);
        session()->put('recently_viewed', array_slice($recentlyViewed, 0, 5));
    
        // Lấy danh sách bình luận của sản phẩm hiện tại (chỉ lấy bình luận cha)
        $reviews = ProductComment::with('user', 'replies.user')
            ->where('product_id', $product->id) // lọc theo sản phẩm
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();
    
        return view('products.show', compact('product', 'relatedProducts', 'reviews'));
    }
    
    




}
