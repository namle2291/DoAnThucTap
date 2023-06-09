<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];
    public function setting()
    {
        return $this->hasOne(ThemeSetting::class, 'user_id', 'id');
    }
    public function post()
    {
        return $this->hasMany(Post::class, 'user_id', 'id');
    }
    public function comment()
    {
        return $this->hasMany(Comment::class, 'customer_id', 'id');
    }
    public function reply_comment()
    {
        return $this->hasMany(Reply::class, 'customer_id', 'id');
    }
}
