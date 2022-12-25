<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use SoftDeletes;

    public static $preventAttrSet = false;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'ativo',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    //RELATIONSHIPS
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }


    //MUTATORS
    public function getIsAdminAttribute()
    {
        if (self::$preventAttrSet) {
        } else {
            if (in_array(1, auth()->user()->roles->pluck('id')->toArray())) {
                return true;
            }
        }

        return false;
    }
}
