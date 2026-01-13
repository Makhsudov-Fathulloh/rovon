<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'address',
        'password_hash',
        'email',
        'photo',
        'phone',
        'telegram_chat_id',
        'role_id',
        'status',
        'debt',
        'token',
        'auth_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
        'token',
        'auth_key',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password_hash' => 'hashed',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->role_id)) {
                $user->role_id = Role::where('title', 'Client')->value('id');
            }
        });
    }

    public function avatar()
    {
        return $this->belongsTo(File::class, 'photo');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function orderItems()
    {
        return $this->hasManyThrough(OrderItem::class, Order::class, 'user_id', 'order_id', 'id', 'id');
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'shift_user', 'user_id', 'shift_id')
            ->using(ShiftUser::class)
            ->withTimestamps();
    }

    public function shiftUser()
    {
        return $this->hasMany(ShiftUser::class, 'user_id', 'id')->from('shift_user');
    }

    public function shiftOutputWorker()
    {
        return $this->hasMany(ShiftOutputWorker::class);
    }
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function userDebt()
    {
        return $this->hasMany(UserDebt::class);
    }

    public function organization()
    {
        return $this->belongsToMany(Organization::class, 'organization_user')->withTimestamps();
    }

    protected function sanitizePhone(?string $phone): ?string
    {
        if (!$phone) return null;

        // faqat raqamlarni olish
        $digits = preg_replace('/\D/', '', $phone);

        // agar 998 bilan boshlansa, + qo‘shamiz
        if (Str::startsWith($digits, '998') && strlen($digits) == 12) {
            return '+' . $digits;
        }

        return null; // noto‘g‘ri format bo‘lsa
    }
}
