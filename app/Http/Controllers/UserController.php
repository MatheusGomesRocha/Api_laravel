<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private $response = ['error' => '', 'result' => []];

    public function getUsers()
    {
        $users = User::getUsers();

        $cart = Cart::getCart('27');

        foreach ($users as $query) {
            $this->response['result'][] = [
                'nome' => $query->name,
                'email' => $query->email,
            ];
        }

        $teste = '';

        if(Auth::check()) {
            $teste = 'olá mundo';
        } else {
            $teste = 'hehehe';
        }

        // return $this->response;

        return view('welcome')->with('teste', $teste);
    }

    public function login(Request $request)
    {
        $validation = $this->validationLogin($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Campos não preenchidos';
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

    public function registerUser(Request $request)
    {
        $validation = $this->validationRegister($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Campos não preenchidos';
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

    public function delete(Request $request, $user)
    {
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

    public function update(Request $request, $user)
    {
        $validation = $this->validationUpdate($request->all());

        if ($validation->fails()) {
            return $this->response['error'] = 'Campos não preenchidos';
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

    public function getUserLogin($user)
    {
        $userInfo = User::getUser($user);

        if ($userInfo) {
            $this->response['result'] = [
                'avatar' => url('storage/media/avatars/' . $userInfo->avatar),
                'name' => $userInfo->name,
                'user' => $userInfo->user,
                'email' => $userInfo->email,
                'password' => $userInfo->password,
            ];
        }

        return $this->response;
    }

    public function updateAvatar(Request $request)
    {
        $image = $request->file('avatar');
        $user = $request->input('user');

        if ($image) {
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $img = rand();

                $extensao = $request->file('avatar')->extension();
                $file = "$img.$extensao";
                $upload = $request->file('avatar')->storeAs('public/media/avatars/', $file);

                DB::table('users')->where('user', '=', $user)->update([
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

}
