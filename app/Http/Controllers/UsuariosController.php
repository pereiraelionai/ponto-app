<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsuariosController extends Controller
{
    public function getView()
    {
        return view('usuarios');
    }

    // Rotas API
    // -----------------------------------------------------------------------------------

    public function index()
    {   
        $usuarios = User::orderByDesc('id')->paginate(10);
        return response()->json(['usuarios' => $usuarios]);
    } 
    
    public function consultaUsuario(Request $request)
    {
        $usuario = User::where('name', $request->input('usuario'))->first();

        if(empty($usuario)) {
            return response(true);
        } else {
            return response(false);
        }
    }

    public function store(Request $request) 
    {   
        $regras = [
            'usuario' => 'required|max:255|unique:users,name',
            'password' => 'required|confirmed',
            'email' => 'required|email|unique:users,email',
            'tp_usuario' => 'required'
        ];

        $msg = [
            'required' => 'O :attribute é obrigatório',
            'max' => 'Tamanho máximo excedido',
            'unique' => 'Usuário já cadastrado',
            'confirmed' => 'As senhas precisam ser iguais'
        ];

        $request->validate($regras, $msg);

        $usuario = new User();
        $usuario->name = $request->input('usuario');
        $usuario->tp_usuario = $request->input('tp_usuario');
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt($request->input('password'));
        $usuario->save();

        if(!$usuario->id) {
            return response()->json(['errors' => 'Erro ao salvar usuário'], 422);
        }

        return response()->json(['usuario' => $usuario], 201);

    }

    public function destroy($id)
    {   
        $usuario = User::find($id);
        $usuario->delete();
        return response()->json(['msg' => 'Usuário excluído com sucesso'], 200);
    }     
}
