<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    public static function insertCart($userId, $productId, $productName, $productImg, $productPrice, $productQuantity)
    {
        return DB::table('cart')->insert([
            'userId' => $userId,
            'productId' => $productId,
            'name' => $productName,
            'img' => $productImg,
            'price' => $productPrice,
            'quantity' => $productQuantity,
        ]);
    }

    public static function getCart($userId) {
        return DB::table('cart')->where('userId', '=', $userId)->get();
    }
}
