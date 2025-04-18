<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductComment;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    // Hiển thị danh sách bình luận (có phân trang)
    public function index()
    {
        // Lấy các bình luận cha kèm thông tin sản phẩm, người dùng và bình luận con (replies)
        $comments = ProductComment::with('product', 'user', 'replies')
                    ->whereNull('parent_id')
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                    
        return view('admin.comments.index', compact('comments'));
    }
    
    // Hiển thị form chỉnh sửa bình luận
    public function edit($id)
    {
        $comment = ProductComment::findOrFail($id);
        return view('admin.comments.edit', compact('comment'));
    }

    // Cập nhật bình luận
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:2',
        ]);

        $comment = ProductComment::findOrFail($id);
        $comment->update($request->only('rating', 'comment'));

        return redirect()->route('admin.comments.index')->with('success', 'Bình luận đã được cập nhật.');
    }

    // Xóa bình luận
    public function destroy($id)
    {
        $comment = ProductComment::findOrFail($id);
        $comment->delete();

        return redirect()->route('admin.comments.index')->with('success', 'Bình luận đã được xóa.');
    }
}
