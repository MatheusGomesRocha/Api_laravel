<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    public static function newProduct($img, $name, $price, $description, $category, $time) {
        return DB::table('products')->insert([
            'img' => $img,
            'name' => $name,
            'price' => $price,
            'description' => $description,
            'category' => $category,
            'time' => $time,
            'rate' => ''
        ]);
    }

    public static function products() {
        return DB::table('products')->get();
    }

    public static function product($id) {
        return DB::table('products')->where('id', '=', $id)->first();
    }
    
    public static function verifyFavorites($userId, $productId) {
        return DB::table('favorites')
        ->where('userId', '=', $userId)
        ->where('productId', '=', $productId)
        ->first();
    }

    public static function getFavorites($userId) {
        return DB::table('favorites')
        ->join('products', 'products.id', '=', 'favorites.productId')
        ->where('userId', '=', $userId)
        ->get();
    }

    public static function setFavorites($userId, $productId) {
        return DB::table('favorites')->insert([
            'userId' => $userId,
            'productId' => $productId
        ]);
    }

    public static function removeFromFavorites($userId, $productId) {
        return DB::table('favorites')
        ->where('userId', '=', $userId)
        ->where('productId', '=', $productId)
        ->delete();
    }
}
