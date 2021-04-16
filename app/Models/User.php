<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user',
        'avatar',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function getUsers()  {
        return DB::table('users')->select('*')->get();
    }

    public static function getUser($user) {
        return DB::table('users')->where('user', '=', $user)->first();
    }

    public static function updateUser($name, $user, $email, $password) {
        return DB::table('users')->where('user', '=', $user)->update([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    public static function deleteUser($user) {
        return DB::table('users')->where('user', '=', $user)->delete();
    }

}
