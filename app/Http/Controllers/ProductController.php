<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    public function createProduct(Request $request) {

        $validation = $this->validationProduct($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Campos não preenchidos';
        }

        $img = $request->file('img');
        $name = $request->input('name');
        $price = $request->input('price');
        $description = $request->input('description');
        $category = $request->input('category');
        $time = $request->input('time');

        if ($img && $img->isValid()) {
            $image = rand();

            $extensao = $img->extension();
            $file = "$image.$extensao";
            $upload = $img->storeAs('public/media/images/', $file);

            $this->response['result'] = url('storage/media/avatars/' . $file);
        } else {
            $this->response['error'] = 'File not supported';
        }

        $createProduct = Product::newProduct($file, $name, $price, $description, $category, $time);


        if($createProduct) {
            $this->response['result'] = 'Product sucessfully created';
        } else {
            $this->response['error'] = 'ERROR - Try again';
        }

        return $this->response;
    }

    public function getProducts() {
        $products = Product::products();

        if($products->count() === 0) {
            $this->response['error'] = 'Ainda não tem nenhum item cadastrado';
        } else {
            foreach($products as $query) {
                $this->response['result'][] = [
                    'id' => $query->id,
                    'img' => $query->img,
                    'name' => $query->name,
                    'price' => $query->price,
                    'description' => $query->description,
                    'category' => $query->category,
                    'rate' => $query->rate,
                    'time' => $query->time,
                ];
            }
        }

        return $this->response;
    }

    public function getOneProduct($id) {
        $product = Product::product($id);

        if($product) {
            $this->response['result'] = [
                $product
            ];
        } else {
            $this->response['error'] = 'Sorry, something went wrong';
        }

        return $this->response;
    }

    public function toFavorites(Request $request) {
        $productId = $request->input('productId');
        $userId = $request->input('userId');
        
        $exists = Product::getFavoritesIfExists($userId, $productId);
        $product = Product::product($productId);

        // Verifica se tem algum produto com o ID enviado
        if(!$product) {
            $this->response['error'] = 'Sorry, you try to added an product that do not exist';
        } else {    
            // Verifica se já existe esse produto enviado na lista do usuário enviado
            if($exists->count() === 0) {
                $setFavorite = Product::setFavorites($userId, $productId);
                
                if($setFavorite) {
                    $this->response['result'] = 'This product is now on your favorite list';
                } else {
                    $this->response['error'] = 'Sorry, something went wrong';
                }
            } else {
                $this->response['error'] = "You've already added this product";
            }    
        }

        return $this->response;
    }

    public function getFavorites(Request $request) {
        $userId = $request->input('userId');

        $favorites = Product::getFavorites($userId);

        if($favorites->count() === 0) {
            $this->response['error'] = "Sorry, you didn't added any product to your favorite list";
        } else {
            foreach($favorites as $query) {
                $this->response['result'][] = [
                    'name' => $query->name,
                    'price' => $query->price,
                    'img' => $query->img,
                ];
            }
        }

        return $this->response;
    }



    private function validationProduct($data)
    {
        $regras = [
            'img' => 'required',
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'category' => 'required',
            'time' => 'required',
        ];

        $mensagens = [

        ];

        return Validator::make($data, $regras, $mensagens);
    }
}
