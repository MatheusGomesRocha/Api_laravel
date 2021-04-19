<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    public static function insertCart($userId, $productId, $productQuantity)
    {
        return DB::table('cart')->insert([
            'userId' => $userId,
            'productId' => $productId,
            'quantity' => $productQuantity,
        ]);
    }

    public static function getCart($userId)
    {
        return DB::table('cart')
            ->join('products', 'products.id', '=', 'cart.productId')
            ->where('cart.userId', '=', $userId)
            ->get();
    }

    public static function getCartToOrder($userId)
    {
        return DB::table('products')
        ->join('cart', 'cart.productId', '=', 'products.id')
        ->select('products.name', 'products.price', 'cart.quantity')
        ->where('cart.userId', '=', $userId)
        ->get();
    }

    public static function makeOrder($cart, $subtotal, $userId)
    {
        return DB::table('orders')->insert([
            'orderInfo' => $cart,
            'subtotal' => $subtotal,
            'userId' => $userId
        ]);
    }

    public static function removeFromCart($id) {
        return DB::table('cart')->where('id', '=', $id)->delete();
    }
}
