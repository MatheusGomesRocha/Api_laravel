<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    public function insertCart(Request $request) {
        $userId = $request->input('userId');
        $productId = $request->input('productId');
        $productName = $request->input('name');
        $productImg = $request->input('img');
        $productPrice = $request->input('price');
        $productQuantity = $request->input('quantity');

        $insert = Cart::insertCart($userId, $productId, $productName, $productImg, $productPrice, $productQuantity);

        if($insert) {
            $this->response['result'] = ['Added to cart'];
        } else {
            $this->response['error'] = 'Sorry, something went wrong';
        }

        return $this->response;
    }

    public function getCart($userId) {
        $cart = Cart::getCart($userId);

        if($cart->count() === 0) {
            $this->response['error'] = 'Sorry, something went wrong';
        } else {
            $this->response['result'] = [
                $cart,
            ];
        }

        return $this->response;
    }
}
