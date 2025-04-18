<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    protected $fillable = ['product_id', 'user_id', 'rating', 'comment', 'parent_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ bình luận cha (nếu có)
    public function parent()
    {
        return $this->belongsTo(ProductComment::class, 'parent_id');
    }

    // Lấy danh sách phản hồi cho bình luận này
    public function replies()
    {
        return $this->hasMany(ProductComment::class, 'parent_id');
    }
}
