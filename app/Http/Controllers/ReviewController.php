<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReviewController extends Controller
{


    public function store(Request $request, $categoryId)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:2',
        ]);

        // Lấy thông tin danh mục cần đánh giá
        $category = Category::findOrFail($categoryId);

        // Tạo đánh giá mới, nếu người dùng không đăng nhập thì user_id = null (ẩn danh)
        $comment = Review::create([
            'category_id' => $category->id,
            'user_id'     => Auth::check() ? Auth::id() : null,
            'rating'      => $request->input('rating'),
            'comment'     => $request->input    ('comment'),
        ]);

        // Chuyển hướng về URL cũ kèm anchor trỏ đến bình luận vừa tạo
        return redirect(url()->previous() . '#comment-' . $comment->id)
               ->with('success', 'Đánh giá sản phẩm của bạn đã được gửi.');
    }
    public function storecmt(Request $request, $productId)
    {
        // Validate dữ liệu đầu vào từ form
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:2',
        ]);
    
        // Lấy thông tin sản phẩm cần bình luận
        $product = Product::findOrFail($productId);
    
        // Tạo bình luận mới cho sản phẩm, lưu vào biến $comment
        $comment = ProductComment::create([
            'product_id' => $product->id,
            'user_id'    => Auth::check() ? Auth::id() : null,
            'rating'     => $request->input('rating'),
            'comment'    => $request->input('comment'),
        ]);
    
        // Chuyển hướng về URL cũ kèm anchor trỏ đến bình luận vừa tạo
        return redirect(url()->previous() . '#comment-' . $comment->id)
               ->with('success', 'Đánh giá sản phẩm của bạn đã được gửi.');
    }
    

    public function reply(Request $request, $commentId)
    {
        // Xác thực dữ liệu nhập vào (chỉ cần comment, vì reply không cần rating)
        $request->validate([
            'comment' => 'required|string|min:2',
        ]);
    
        // Lấy bình luận cha dựa trên $commentId
        $parentComment = ProductComment::findOrFail($commentId);
    
        // Tạo phản hồi mới, lưu vào biến $reply
        $reply = ProductComment::create([
            'product_id' => $parentComment->product_id,
            'user_id'    => Auth::check() ? Auth::id() : null,
            'comment'    => $request->input('comment'),
            'parent_id'  => $parentComment->id,
            // Nếu bạn cho phép chỉ bình luận chính có rating, thì reply không cần rating hoặc để null
            'rating'     => null,
        ]);
    
        // Chuyển hướng về trang cũ (có thể kèm anchor nếu muốn trỏ đến reply vừa gửi)
        return redirect(url()->previous() . '#comment-' . $reply->id)
               ->with('success', 'Phản hồi của bạn đã được gửi.');
    }
    
    public function destroy($id)
    {
        $comment = ProductComment::findOrFail($id);
        
        // Lưu anchor của bình luận trước khi xóa
        $anchor = 'comment-' . $comment->id;
        
       
        $comment->delete();
        
        // Chuyển hướng về URL cũ với anchor
        return redirect(url()->previous() . '#' . $anchor)
               ->with('success', 'Bình luận/Phản hồi đã được xóa thành công.');
    }
    

}
