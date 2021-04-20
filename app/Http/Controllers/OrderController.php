<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    // Pega os pedidos do usuário logado
    public function getOrder($userId) {
        $userId = $request->input('userId');

        $orders = Order::getOrders($userId);

        foreach($orders as $query) {
            $this->response['result'][] = [
                $query
            ]; 
        }

        return $this->response;
    }
}
