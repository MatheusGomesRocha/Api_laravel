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

    // Pega usuário logado
    public static function getUser($userId) {
        return DB::table('users')->where('id', '=', $userId)->first();
    }

    // Edita informações do usuário
    public static function updateUser($name, $userId, $email, $password) {
        return DB::table('users')->where('id', '=', $userId)->update([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    // Delete a própria conta
    public static function deleteUser($user) {
        return DB::table('users')->where('user', '=', $user)->delete();
    }

    // Pega os produtos favoritos do usuário
    public static function getFavorites($userId) {
        return DB::table('favorites')
        ->join('products', 'products.id', '=', 'favorites.productId')
        ->where('userId', '=', $userId)
        ->get();
    }

    // Verifica se já existe o produto na lista do usuário na tabela de Favoritos
    public static function verifyFavorites($userId, $productId) {
        return DB::table('favorites')
        ->where('userId', '=', $userId)
        ->where('productId', '=', $productId)
        ->first();
    }

    // Adiciona um produto aos favoritos
    public static function setFavorites($userId, $productId) {
        return DB::table('favorites')->insert([
            'userId' => $userId,
            'productId' => $productId
        ]);
    }

    // Remove dos favoritos
    public static function removeFromFavorites($userId, $productId) {
        return DB::table('favorites')
        ->where('userId', '=', $userId)
        ->where('productId', '=', $productId)
        ->delete();
    }

    // Adiciona 1 endereço (apenas 1 por usuário)
    public static function createAddress($bairro, $rua, $numero, $complemento, $referencia, $userId) {
        return DB::table('address')->insert([
            'userId' => $userId,
            'bairro' => $bairro,
            'rua' => $rua,
            'numero' => $numero,
            'complemento' => $complemento,
            'referencia' => $referencia,
        ]);
    }

    // Remove o endereço
    public static function removeAddress($userId) {
        return DB::table('address')->where('userId', '=', $userId)->delete();
    }

    // Pegar o endereço do usuário
    public static function getAddress($userId) {
        return DB::table('address')->where('userId', '=', $userId)->first();
    }

}
