<?php

namespace App\Http\Controllers;

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

        foreach ($users as $query) {
            $this->response['result'][] = [
                'nome' => $query->name,
                'email' => $query->email,
            ];
        }

        // return $this->response;

        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = [
            'user' => $request->input('user'),
            'password' => $request->input('password')
        ];

        if(Auth::attempt($credentials)) {
            $this->response['result'] = 'Login successfully';
            return $this->response;
        } else {
            $this->response['error'] = "Login doesn't exist";
            return $this->response;
        }
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
                'name' => $request->input('name'),
                'user' => $request->input('user'),
                'email' => $request->input('email'),
                'password' => hash::make($request->input('password')),
            ];

            $create = DB::table('users')->insert($data);

            $this->response['result'][] = [$data];

            return $this->response;
        } else {
            return $this->response['error'] = 'Usuário já cadastrado';
        }
    }

    public function delete($user) {
        $delete = DB::table('users')->where('user', '=', $user)->delete();

        if($delete) {
            $this->response['result'] = 'User deleted';
        } else {
            $this->response['error'] = 'ERROR - try again';
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
            'password' => 'required|min:6',
        ];

        $mensagens = [
            'user.required' => 'Preencha o campo usuário',
            'password.required' => 'Preencha o campo senha',
        ];

        return Validator::make($data, $regras, $mensagens);
    }

}
