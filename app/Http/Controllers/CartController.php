<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CartController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    public function insertCart(Request $request) {
        $userId = $request->input('userId');
        $productId = $request->input('productId');
        $productQuantity = $request->input('quantity');

        $insert = Cart::insertCart($userId, $productId, $productQuantity);

        if($insert) {
            $this->response['result'] = 'Added to cart';
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
            foreach($cart as $query) {
                $this->response['result'][] = [
                    'id' => $query->id,
                    'productId' => $query->productId,
                    'name' => $query->name,
                    'price' => $query->price,
                    'img' => $query->img,
                ];
            }
        }

        return $this->response;
    }

    public function makeOrder(Request $request) {
        $userId = $request->input('userId');
        $subtotal = $request->input('subtotal');

        $cart = Cart::getCartToOrder($userId);

        $makeOrder = Cart::makeOrder($cart, $subtotal, $userId);

        if($makeOrder) {
            $this->response['result'] = 'Success, just wait to your products';
        } else {
            $this->response['error'] = 'Sorry, something went wrong';
        }

        return $this->response;
    }
}
