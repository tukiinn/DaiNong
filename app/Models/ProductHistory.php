<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductHistory extends Model
{
    protected $fillable = ['product_id', 'action', 'user_id', 'data', 'old_data'];

    protected $casts = [
        'data' => 'array',
        'old_data' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}

