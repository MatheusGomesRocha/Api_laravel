<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    // Adiciona um novo produto
    public function createProduct(Request $request) {

        $validation = $this->validationProduct($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Preencha todos os campos com *';
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

    // Pega todos os produtos
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

    // Pega apenas o produto selecionado
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


    // Validação dos Inputs ao adicionar um novo produto
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
