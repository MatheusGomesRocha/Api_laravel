<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
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
            $this->response['result'] = [
                $products
            ];
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
