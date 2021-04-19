<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    public function getOrder() {
        $orders = Order::getOrders('27');

        $result = json_decode($orders);

        foreach($result as $query) {

            $this->response['result'][] = [
                'order' => $query
            ]; 
        }

        return $this->response;
    }
}
