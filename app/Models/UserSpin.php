<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSpin extends Model
{
    use HasFactory;

    // Nếu tên bảng không theo quy ước (user_spins) thì khai báo:
    protected $table = 'user_spins';

    /**
     * Các trường có thể gán giá trị hàng loạt.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'prize',  // Thông tin phần quà nhận được
    ];

    /**
     * Mối quan hệ: Một lượt quay thuộc về một người dùng.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
