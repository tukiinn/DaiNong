<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\News;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Auth;
class NAController extends Controller
{

    public function liveSearch(Request $request)
    {
        $query = $request->input('query');
        $dataType = $request->input('dataType', 'all');
    
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }
    
        $results = [];
    
        if ($dataType === 'all' || $dataType === 'products') {
            $products = Product::where('product_name', 'like', '%' . $query . '%')->get()->map(function ($product) {
                $product->image = $product->image ? asset($product->image) : 'https://via.placeholder.com/60';
                return $product;
            });
            $results['products'] = $products;
        }
    
        if ($dataType === 'all' || $dataType === 'categories') {
            $categories = Category::where('category_name', 'like', '%' . $query . '%')->get()->map(function ($cat) {
                $cat->image = $cat->image ? asset($cat->image) : null;
                return $cat;
            });
            $results['categories'] = $categories;
        }
    
        if (($dataType === 'all' || $dataType === 'orders') && Auth::check()) {
            $userId = Auth::id();
            $orders = Order::where('user_id', $userId)
                ->where(function ($q) use ($query) {
                    $q->where('ten_khach_hang', 'like', '%' . $query . '%')
                      ->orWhere('id', $query);
                })
                ->get();
            $results['orders'] = $orders;
        }
    
        if ($dataType === 'news' || $dataType === 'all') {
            $news = News::where('title', 'like', '%' . $query . '%')->get()->map(function ($newsItem) {
                $newsItem->image = $newsItem->image ? asset($newsItem->image) : null;
                return $newsItem;
            });
            $results['news'] = $news;
        }
    
        return response()->json($results);
    }
public function submit(Request $request)
    {
        // Validate dữ liệu từ form
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'nullable|string',
        ]);

        // Gửi email tới địa chỉ mà bạn muốn nhận thông báo
        Mail::to('donhotu03.dev@gmail.com')->send(new ContactMail($data));

        return redirect()->back()->with('success', 'Liên hệ của bạn đã được gửi thành công.');
    }

    public function search(Request $request)
    {
        // Validate input: từ khóa phải có độ dài tối thiểu và không quá dài
        $request->validate([
            'query' => 'required|string|min:2|max:255',
        ]);
    
        $query = $request->input('query');
    
        // Tạo key cho cache (bao gồm cả trang hiện tại)
        $page = $request->input('page', 1);
        $cacheKey = 'search_' . md5($query . '_' . $page);
    
        // Sử dụng cache trong 60 giây nếu truy vấn lặp lại
        $products = Cache::remember($cacheKey, 60, function() use ($query) {
            return Product::where('product_name', 'like', '%' . $query . '%')
                          ->paginate(8);
        });
    
        return view('products.search-results', compact('products', 'query'));
    }
    



    public function index()
    {
        // Lấy tin tức mới nhất, phân trang 10 bài viết/trang
        $news = News::latest()->paginate(10);
        return view('news.index', compact('news'));
    }

    // Hiển thị chi tiết bài viết tin tức theo slug
    public function show($slug)
    {
        $newsItem = News::where('slug', $slug)->firstOrFail();
        return view('news.show', compact('newsItem'));
    }
    public function addressIndex()
    {
       
        return view('address.index');
    }
   
}
