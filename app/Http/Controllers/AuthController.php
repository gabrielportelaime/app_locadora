<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){
        //autenticação do usuário (email e senha)
        $token = auth('api')->attempt($request->all(['email', 'password']));
        if($token){
            //retornar um jwt
            return response()->json(['token' => $token], 200);
        }else{
            return response()->json(['erro' => 'Usuário ou senha inválidos'], 403);
        }
        //401 - Não autorizado - Questão de autorizações dentro do app
        //403 - Proibido - Não pode acessar por não estar logado
    }
    public function logout(){
        auth('api')->logout();
        return response()->json(['msg' => 'Você foi deslogado com sucesso!']);
    }
    public function refresh(){
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);
    }
    public function me(){
        return response()->json(auth()->user());
    }
}
