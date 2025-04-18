<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Searchable;
    use HasFactory;

    // Tên bảng trong database
    protected $table = 'products';

    // Khóa chính
    protected $primaryKey = 'id';

    // Cho phép Laravel tự động thêm cột `created_at` và `updated_at`
    public $timestamps = true;

    // Các cột có thể điền dữ liệu (mass assignable)
    protected $fillable = [
        'product_name',
        'description',
        'image',
        'price',
        'import_price',
        'discount_price',
        'stock_quantity',
        'supplier_id', // Tham chiếu đến nhà cung cấp
        'unit_id',     // Tham chiếu đến đơn vị
        'category_id',
        'slug',
        'status',
        'harvest_season',
        'region',
        'certifications',
        'expiry_date',
        'featured'
    ];

    protected $casts = [
        'expiry_date' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];
    
    /**
     * Quan hệ với bảng Category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    
    /**
     * Quan hệ với bảng Supplier.
     */
    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class, 'supplier_id');
    }
    
    /**
     * Quan hệ với bảng Unit.
     */
    public function unit()
    {
        return $this->belongsTo(\App\Models\Unit::class, 'unit_id');
    }

    /**
     * Quan hệ với bảng OrderItem.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    /**
     * Quan hệ với bảng Cart.
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Quan hệ với bảng Review.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Quan hệ với bảng ProductComment.
     */
    public function comments()
    {
        return $this->hasMany(ProductComment::class);
    }
}
