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
}
