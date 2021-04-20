<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Variável que retorna as querys, error quando dá algum erro, e result quando dá tudo certo
    private $response = ['error' => '', 'result' => []];


    // Função que realiza o login, retornando as informações do usuário caso tenha feito o login
    // corretamente
    public function login(Request $request) {
        $validation = $this->validationLogin($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Preencha todos os campos com *';
        }

        $credentials = [
            'user' => $request->input('user'),
            'password' => $request->input('password')
        ];

        if (Auth::attempt($credentials)) {
            if(Auth::check()) {
                $userInfo = User::getUser($request->input('user'));

                $this->response['result'] = [
                    'id' => $userInfo->id,
                    'avatar' => url('storage/media/avatars/' . $userInfo->avatar),
                    'user' => $userInfo->user,
                ];    
            }
            
        } else {
            $this->response['error'] = "Login doesn't exist";
        }

       return $this->response;
    }

    // Registra um novo usuário
    public function registerUser(Request $request) {
        $validation = $this->validationRegister($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Preencha todos os campos com *';
        }

        $hasUser = DB::table('users')->select('*')->where('user', '=', $request->input('user'))->count();

        if ($hasUser == 0) {
            $data = [
                'avatar' => '',
                'name' => $request->input('name'),
                'user' => $request->input('user'),
                'email' => $request->input('email'),
                'password' => hash::make($request->input('password')),
            ];

            $create = DB::table('users')->insert($data);

            $this->response['result'][] = [$data];
        } else {
            $this->response['error'] = 'Usuário já cadastrado';
        }

        return $this->response;
    }

    // Delete a própria conta do usuário
    public function delete(Request $request, $user) {
        $userInfo = User::getUser($user);
        $password = $request->input('password');

        if ($userInfo) {
            if (Hash::check($password, $userInfo->password)) {
                $delete = User::deleteUser($user);

                $this->response['result'] = 'User deleted';
            } else {
                $this->response['error'] = 'Sorry, wrong password';
            }
        } else {
            $this->response['error'] = 'User not found';
        }


        return $this->response;
    }

    // Faz um update de nome, email ou senha. Pedindo a confirmação da senha para ficar seguro
    public function update(Request $request, $user) {
        $validation = $this->validationUpdate($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Preencha todos os campos com *';
        }

        $userInfo = User::getUser($user);

        $password = $request->input('password');

        if ($request->input('name')) {
            $name = $request->input('name');
        } else {
            $name = $userInfo->name;
        }

        if ($request->input('email')) {
            $email = $request->input('email');
        } else {
            $email = $userInfo->email;
        }

        if ($request->input('new_password')) {
            $newPassword = $request->input('new_password');
        } else {
            $newPassword = $password;
        }

        if ($userInfo) {
            if (Hash::check($password, $userInfo->password)) {
                User::updateUser($name, $user, $email, $newPassword);
                $this->response['result'] = [
                    'name' => $name,
                    'user' => $user,
                    'email' => $email,
                    'password' => $newPassword
                ];
                $this->response;
            } else {
                $this->response['error'] = 'Incorrect Password';
                $this->response;
            }
        } else {
            $this->response['error'] = 'User not found';
        }

        return $this->response;
    }

    // Pega o usuário logado
    public function getUserLogin($userId) {
        $userInfo = User::getUser($userId);

        if ($userInfo) {
            $this->response['result'] = [
                'id' => $userInfo->id,
                'avatar' => url('storage/media/avatars/' . $userInfo->avatar),
                'name' => $userInfo->name,
                'user' => $userInfo->user,
                'email' => $userInfo->email,
                'password' => $userInfo->password,
            ];
        }

        return $this->response;
    }

    // Edita o avatar do usuário
    public function updateAvatar(Request $request, $userId) {
        $image = $request->file('avatar');

        if ($image) {
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $img = rand();

                $extensao = $request->file('avatar')->extension();
                $file = "$img.$extensao";
                $upload = $request->file('avatar')->storeAs('public/media/avatars/', $file);

                DB::table('users')->where('id', '=', $userId)->update([
                    'avatar' => $file,
                ]);

                $this->response['result'] = url('storage/media/avatars/' . $file);
            } else {
                $this->response['error'] = 'File not supported';
            }
        } else {
            $this->response['error'] = 'Send a file';
        }

        return $this->response;
    }

    // Pega os produtos favoritos do usuário logado
    public function getFavorites(Request $request, $userId) {
        $favorites = User::getFavorites($userId);

        if($favorites->count() === 0) {
            $this->response['error'] = "Ooh... you didn't added any product to your favorite list";
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

    // Insere um produto aos favoritos
    public function toFavorites(Request $request, $userId) {
        $productId = $request->input('productId');
        
        $exists = User::verifyFavorites($userId, $productId);
        $product = Product::product($productId);

        // Verifica se tem algum produto com o ID enviado
        if(!$product) {
            $this->response['error'] = 'Sorry, you try to added an product that do not exist';
        } else {    
            // Verifica se já existe esse produto enviado na lista do usuário enviado
            if(!$exists) {
                $setFavorite = User::setFavorites($userId, $productId);
                
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

    // Deleta um produto dos favoritos
    public function removeFromFavorites(Request $request, $userId) {
        $productId = $request->input('productId');

        $verify = User::verifyFavorites($userId, $productId);

        if(!$verify) {
            $this->response['error'] = 'Sorry, something went wrong';
        } else {
            $delete = User::removeFromFavorites($userId, $productId);

            if($delete) {
                $this->response['result'] = '';
            } else {
                $this->response['error'] = "Sorry, couldn't deleted";
            }
        }

        return $this->response;
    }

    // Cria um endereço para usar nos pedidos (somente 1 por usuário)
    public function createAddress(Request $request, $userId) {
        $validation = $this->validationAddress($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Preencha todos os campos com *';
        }

        $bairro = $request->input('bairro');
        $rua = $request->input('rua');
        $numero = $request->input('numero');
        $complemento = $request->input('complemento');
        $referencia = $request->input('referencia');

        if($numero) {
            $complemento = '';
        } 
        if($complemento) {
            $numero = '';
        } 
        if(!$referencia) {
            $referencia = '';
        } 

        $userLogin = User::getUserLogin($userId);

        if($userLogin === 0) {
            $this->response['error'] = "You only can add 1 address";
        } else {
            $insert = User::createAddress($bairro, $rua, $numero, $complemento, $referencia, $userId);

            if($insert) {
                $this->response['result'] = 'Address added successfully';
            } else {
                $this->response['error'] = 'Sorry, try again';
            }
        }
        

        return $this->response;
    }

    // Deleta o endereço do usuário
    public function removeAddress($userId) {
        $delete = User::removeAddress($userId);

        if($delete) {
            $this->response['result'] = true;
        } else {
            $this->response['error'] = 'Sorry, try again';
        }
        
        return $this->response;
    }

    // Pega o endereço do usuário
    public function getAddress($userId) {
        $address = User::getAddress($userId);

        if(!$address) {
            $this->response['error'] = "Please add a address";
        } else {
            $this->response['result'][] = [
                'bairro' => $address->bairro,
                'rua' => $address->rua,
                'numero' => $address->numero,
                'complemento' => $address->complemento,
                'referencia' => $address->referencia,
            ]; 
        }

        return $this->response;
    }



    // Validação dos inputs do Cadastro de Usuários
    private function validationRegister($data)
    {
        $regras = [
            'name' => 'required',
            'user' => 'required',
            'email' => 'required',
            'password' => 'required|min:6',
        ];

        $mensagens = [
            'name.required' => 'Preencha o campo nome',
            'user.required' => 'Preencha o campo usuário',
            'email.required' => 'Preencha o campo email',
            'password.required' => 'Preencha o campo senha',
            'password.min' => 'A senha precisa ter no mínimo 6 caracteres',
        ];

        return Validator::make($data, $regras, $mensagens);
    }

    // Validação dos inputs do Login
    private function validationLogin($data)
    {
        $regras = [
            'user' => 'required',
            'password' => 'required',
        ];

        $mensagens = [
            'user.required' => 'Preencha o campo usuário',
            'password.required' => 'Preencha o campo senha',
        ];

        return Validator::make($data, $regras, $mensagens);
    }

    // Validação dos inputs da Edição das informações do Usuário
    private function validationUpdate($data)
    {
        $regras = [
            'password' => 'required',
        ];

        $mensagens = [
            'password.required' => 'Preencha o campo senha',
        ];

        return Validator::make($data, $regras, $mensagens);
    }

    // Validação dos inputs do Endereço
    private function validationAddress($data)
    {
        $regras = [
            'bairro' => 'required',
            'rua' => 'required',
        ];

        $mensagens = [
            'bairro.required' => 'Preencha o campo usuário',
            'rua.required' => 'Preencha o campo senha',
        ];

        return Validator::make($data, $regras, $mensagens);
    }

}
