<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Cart extends Model
{
    use HasFactory;

    // Insere um produto no carrinho
    public static function insertCart($userId, $productId, $productQuantity)
    {
        return DB::table('cart')->insert([
            'userId' => $userId,
            'productId' => $productId,
            'quantity' => $productQuantity,
        ]);
    }

    // Verifica se o usuário já adicionou esse produto ao carrinho
    public static function verifyProductAndUser($userId, $productId) {
        return DB::table('cart')
        ->where('userId', '=', $userId)
        ->where('productId', '=', $productId)
        ->first();
    }

    // Pega os produtos do carrinho
    public static function getCart($userId)
    {
        return DB::table('cart')
            ->join('products', 'products.id', '=', 'cart.productId')
            ->where('cart.userId', '=', $userId)
            ->get();
    }

    /* Pega o ID do produto que está no Cart, compara com os o ID do produto que está em Product
        e retorna as informações do produto para inserir no Orders 
    */ 
    public static function getCartToOrder($userId)
    {
        return DB::table('products')
        ->join('cart', 'cart.productId', '=', 'products.id')
        ->select('products.name', 'products.price', 'cart.quantity')
        ->where('cart.userId', '=', $userId)
        ->get();
    }

    // Faz o pedido, inserindo numa tabela Orders
    public static function makeOrder($cart, $subtotal, $userId)
    {
        return DB::table('orders')->insert([
            'orderInfo' => $cart,
            'subtotal' => $subtotal,
            'userId' => $userId
        ]);
    }

    // Remove um produto do carrinho
    public static function removeFromCart($id) {
        return DB::table('cart')->where('id', '=', $id)->delete();
    }
}
