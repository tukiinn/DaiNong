<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Thêm dòng này để sử dụng phân quyền

/**
 * @method bool update(array $attributes = [], array $options = [])
 * @method bool save(array $options = [])
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reviews()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany orders()
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Sử dụng HasRoles cùng với các trait khác

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // Nếu bạn đã lưu trữ role trực tiếp thì có thể xóa 'role' để sử dụng bảng role của spatie
        'role',
        'google_id',
        'avatar',
        'phone', 
        'address', 
        'gender', 
        'date_of_birth'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }
    
    public function vouchers()
    {
        return $this->belongsToMany(\App\Models\Voucher::class, 'user_voucher')
                    ->withTimestamps()
                    ->withPivot('expiry_date');
    }
    
    public function spins()
    {
        return $this->hasMany(\App\Models\UserSpin::class);
    }
    
    public function productComments()
    {
        return $this->hasMany(ProductComment::class);
    }
    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
