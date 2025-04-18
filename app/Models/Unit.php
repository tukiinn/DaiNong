<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    // Các trường có thể được gán hàng loạt
    protected $fillable = [
        'unit_name',
    ];

    /**
     * Quan hệ với sản phẩm.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
