<?php

namespace App\Models;

use App\Transformers\UserTransfomer;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;



    const VERIFIED_USER = '1';
    const UNVERIFIED_USER = '0';

    const ADMIN_USER = 'true';
    const REGULAR_USER = 'false';

    public $transformer = UserTransfomer::class;

    protected $table = 'users';
    protected $dates = ['delated_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function isVerified()
    {
        return $this->verified == User::VERIFIED_USER;
    }


    public function isAdmin()
    {
        return $this->verified == User::ADMIN_USER;
    }


    public static function generateVerificationCode()
    {
        return Str::random(40);
    }


    public function setNameAttribute($name)
    {
        $this->attributes['name'] = strtolower($name);
    }


    public function getNameAttribute($name)
    {
        return ucwords($name);
    }


    public function setEmailAttribute($name)
    {
        $this->attributes['email'] = $name;
    }
}
