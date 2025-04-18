<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductHistory;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
class AdminProductController extends Controller
{
    // Hiển thị danh sách sản phẩm
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Kiểm tra nếu có từ khóa tìm kiếm
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('product_name', 'like', "%$search%");
        }

        $products = $query->paginate(12); // 12 sản phẩm mỗi trang

        return view('admin.products.index', compact('products'));
    }

    public function show($id)
    {
        // Lấy thông tin sản phẩm theo ID
        $products = Product::findOrFail($id);

        // Trả về view hiển thị chi tiết sản phẩm
        return view('admin.products.show', compact('products'));
    }

    // Trang tạo mới sản phẩm
    public function create()
    {
        $categories = Category::all();
        $suppliers  = \App\Models\Supplier::all();
        $units      = \App\Models\Unit::all();
        return view('admin.products.create', compact('categories', 'suppliers', 'units'));
    }

    // Lưu sản phẩm mới vào cơ sở dữ liệu
    public function store(Request $request)
{
    $request->validate([
        'product_name'     => 'required|string|max:255',
        'description'      => 'nullable|string',
        'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'price'            => 'required|numeric|min:0',
        'import_price'     => 'required|numeric|min:0',
        'discount_price'   => 'nullable|numeric|min:0|lte:price',
        'stock_quantity'   => 'required|numeric|min:0',
        'supplier_id'      => 'nullable|exists:suppliers,id',
        'unit_id'          => 'required|exists:units,id',
        'category_id'      => 'required|exists:categories,id',
        'status'           => 'required|boolean',
        'expiry_date'      => 'nullable|date',
        'featured'         => 'boolean',
    ]);

    $data = $request->except('slug');


    $slug = Str::slug($request->product_name);
    $originalSlug = $slug;
    $count = 1;

    while (Product::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $count++;
    }

    $data['slug'] = $slug;


    // Xử lý ảnh
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '_' . $image->getClientOriginalName();
        $image->move(public_path('images/products'), $imageName);
        $data['image'] = 'images/products/' . $imageName;
    }


    $product = Product::create($data);
    ProductHistory::create([
        'product_id' => $product->id,
        'action' => 'create',
        'user_id' => auth()->id(),
        'data' => $product->toArray(),
    ]);

    return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
}

    // Trang chỉnh sửa sản phẩm
    public function edit($id)
    {
        $product    = Product::findOrFail($id);
        $categories = Category::all();
        $suppliers  = \App\Models\Supplier::all();
        $units      = \App\Models\Unit::all();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers', 'units'));
    }

    
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        $request->validate([
            'product_name'     => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'price'            => 'required|numeric|min:0',
            'import_price'     => 'required|numeric|min:0',
            'discount_price'   => 'nullable|numeric|min:0|lte:price',
            'stock_quantity'   => 'required|numeric|min:0',
            'supplier_id'      => 'nullable|exists:suppliers,id',
            'unit_id'          => 'required|exists:units,id',
            'category_id'      => 'required|exists:categories,id',
            'status'           => 'required|boolean',
            'expiry_date'      => 'nullable|date',
            'featured'         => 'boolean',
        ]);
    
        // Dữ liệu trước khi cập nhật
        $originalData = $product->toArray();
    
        // Xử lý slug nếu tên sản phẩm thay đổi
        $data = $request->except('slug');
    
        if ($product->product_name !== $request->product_name) {
            $slug = Str::slug($request->product_name);
            $originalSlug = $slug;
            $count = 1;
    
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
    
            $data['slug'] = $slug;
        } else {
            $data['slug'] = $product->slug;
        }
    
        // Xử lý ảnh
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/products'), $imageName);
            $data['image'] = 'images/products/' . $imageName;
        }
    
        // Cập nhật sản phẩm
        $product->update($data);
    
        // Dữ liệu sau cập nhật
        $updatedData = $product->fresh()->toArray();
    
        // Tìm các trường thay đổi
        $changes = array_diff_assoc($updatedData, $originalData);
    
        // Nếu có thay đổi, lưu lịch sử
        if (!empty($changes)) {
            ProductHistory::create([
                'product_id' => $product->id,
                'action'     => 'update',
                'user_id'    => auth()->id(),
                'data'       => $updatedData,
                'old_data'   => $originalData,
            ]);
        }
    
        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }
    

    // Xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        ProductHistory::create([
            'product_id' => $product->id,
            'action' => 'delete',
            'user_id' => auth()->id(),
            'data' => $product->toArray(),
        ]);
        
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Xóa sản phẩm thành công!');
    }
    public function indexhs(Request $request)
    {
        $histories = ProductHistory::with(['product', 'user'])
            ->when($request->filled('product_name'), function ($query) use ($request) {
                $query->whereHas('product', function ($q) use ($request) {
                    $q->where('product_name', 'like', '%' . $request->product_name . '%');
                });
            })
            ->when($request->filled('user_name'), function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->user_name . '%');
                });
            })
            ->when($request->filled('action'), function ($query) use ($request) {
                $query->where('action', $request->action);
            })
            ->latest()
            ->paginate(20)
            ->appends($request->all()); // giữ query string khi phân trang
    
        return view('admin.products.histories', compact('histories'));
    }
    
public function showhs($id)
{
    $history = ProductHistory::with('product')->findOrFail($id);

    return view('admin.histories.show', compact('history'));
}

}
