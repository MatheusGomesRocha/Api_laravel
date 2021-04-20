<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    // Pega os pedidos feitos pelo o usuário logado
    public static function getOrders($userId) {
        return DB::table('orders')->where('userId', '=', $userId)->get();
    }
}
