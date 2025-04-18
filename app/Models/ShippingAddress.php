<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    /**
     * Các trường được phép điền dữ liệu
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'address_name',
        'province',
        'district',
        'ward',
        'detailed_address',
        'full_address',
        'phone_address',
        'name_address'
    ];

    /**
     * Thiết lập mối quan hệ với model User.
     *
     * Mỗi địa chỉ giao hàng thuộc về một người dùng.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
