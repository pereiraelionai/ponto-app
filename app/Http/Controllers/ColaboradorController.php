<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Colaborador;
use App\Models\Cargo;
use App\Models\Funcao;
use App\Models\User;
use Illuminate\Validation\Rule;

class ColaboradorController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function getView()
    {
        return view('colaboradores');
    }

    //Rotas API
    //-----------------------------------------------------------------------------------

    public function index()
    {   
        $colaboradores = Colaborador::orderByDesc('id')->paginate(10);
        return response()->json(['colaboradores' => $colaboradores]);
    }

    public function store(Request $request) 
    {
        $regras = [
            'matricula' => 'required',
            'nome' => 'required|max:255',
            'cpf' => 'required|max:14|unique:colaboradores,cpf',
            'email' => 'required|email',
            'telefone' => 'required',
            'dt_nascimento' => 'required|date',
            'dt_admissao' => 'required|date',
            'dt_recisao' => 'date|nullable',
            'cargo_id' => 'required|exists:cargos,id',
            'funcao_id' => 'required|exists:funcoes,id',
        ];

        $msg = [
            'required' => 'O :attribute é obrigatório',
            'email' => 'Formato de e-mail inválido',
            'date' => 'Formato de data inválido',
            'exists' => ':attribute inválido',
            'max' => 'Tamanho máximo excedido',
            'unique' => 'CPF já cadastrado  '
        ];

        $request->validate($regras, $msg);

        // Transforma a primeira letra em maiuscula
        $nome = ucwords(strtolower($request->input('nome')));

        $partes = explode(' ', $nome);

        // Verifica se há pelo menos duas partes (Nome e Sobrenome)
        if (count($partes) < 2) {
            return response()->json(['errors' => ['nome' => ['Envie nome e sobrenome']]], 406);
        }

        //formando o usuario
        $usuario = strtolower($partes[0]) . '_' . strtolower($partes[1]);

        //verificar se o usuario existe
        $usuario_colaborador = Colaborador::where('usuario', 'like', $usuario . '%')->orderByDesc('id')->get();
        $qtd_usuarios = count($usuario_colaborador);

        if($qtd_usuarios > 0) {
            preg_match('/(\d+)$/', $usuario_colaborador[0]->usuario, $matches);
            if(isset($matches[1])) {
                $numero = intval($matches[1]) + 1;
                $usuario = $usuario . strval($numero) ;
            } else {
                $usuario = $usuario . strval(1) ;
            }
            
        };

        // data de recisao nao pode ser emnor que a de admissao
        $dataAdmissao = new \DateTime($request->input('dt_admissao'));
        $dataRecisao = new \DateTime($request->input('dt_recisao'));

        // Verifica se a data de recisão é menor que a data de admissão
        if($request->input('dt_recisao') != null) {
            if ($dataRecisao < $dataAdmissao) {
                return response()->json(['errors' => ['dt_recisao' => ['Data de recisão não pode ser menor que a data de admissão']]], 406);
            }
        }

        $colaborador = new Colaborador();
        $colaborador->matricula = $request->input('matricula');
        $colaborador->nome = $nome;
        $colaborador->cpf = $request->input('cpf');
        $colaborador->email = $request->input('email');
        $colaborador->telefone = $request->input('telefone');
        $colaborador->dt_nascimento = $request->input('dt_nascimento');
        $colaborador->dt_admissao = $request->input('dt_admissao');
        $colaborador->dt_recisao = $request->input('dt_recisao');
        $colaborador->cargo_id = $request->input('cargo_id');
        $colaborador->funcao_id = $request->input('funcao_id');
        $colaborador->usuario = $usuario;
        $colaborador->save();

        if(!$colaborador->id) {
            return response()->json(['errors' => 'Erro ao salvar colaborador'], 422);
        }

        //criando usuario
        $usuario = new User();
        $usuario->name = $colaborador->usuario;
        $usuario->email = $request->input('email');
        $usuario->password = bcrypt('mudar@123');
        $usuario->tp_usuario = 'Colaborador';
        $usuario->save();

        if(!$usuario->id) {
            return response()->json(['errors' => 'Erro ao salvar usuario'], 422);
        }

        return response()->json(['colaborador' => $colaborador], 201);

    }

    public function update(Request $request, $id)
    {   
        
        $regras = [
            'matricula' => 'required',
            'nome' => 'required|max:255',
            'email' => 'required|email',
            'telefone' => 'required',
            'dt_nascimento' => 'required|date',
            'dt_admissao' => 'required|date',
            'dt_recisao' => 'date|nullable',
            'cargo_id' => 'required|exists:cargos,id',
            'funcao_id' => 'required|exists:funcoes,id',
        ];

        $msg = [
            'required' => 'O :attribute é obrigatório',
            'email' => 'Formato de e-mail inválido',
            'date' => 'Formato de data inválido',
            'exists' => ':attribute inválido',
            'max' => 'Tamanho máximo excedido',
            'unique' => 'CPF já cadastrado  '
        ];

        $request->validate($regras, $msg);

        // Transforma a primeira letra em maiuscula
        $nome = ucwords(strtolower($request->input('nome')));

        $partes = explode(' ', $nome);

        // Verifica se há pelo menos duas partes (Nome e Sobrenome)
        if (count($partes) < 2) {
            return response()->json(['errors' => ['nome' => ['Envie nome e sobrenome']]], 406);
        }

        //formando o usuario
        $usuario = strtolower($partes[0]) . '_' . strtolower($partes[1]);

        //verificar se o usuario existe
        $usuario_colaborador = Colaborador::where('usuario', 'like', $usuario . '%')->orderByDesc('id')->get();
        $qtd_usuarios = count($usuario_colaborador);

        if($qtd_usuarios > 0) {
            preg_match('/(\d+)$/', $usuario_colaborador[0]->usuario, $matches);
            if(isset($matches[1])) {
                $numero = intval($matches[1]) + 1;
                $usuario = $usuario . strval($numero) ;
            } else {
                $usuario = $usuario . strval(1) ;
            }
            
        };

        // data de recisao nao pode ser emnor que a de admissao
        $dataAdmissao = new \DateTime($request->input('dt_admissao'));
        $dataRecisao = new \DateTime($request->input('dt_recisao'));

        // Verifica se a data de recisão é menor que a data de admissão
        if($request->input('dt_recisao') != null) {
            if ($dataRecisao < $dataAdmissao) {
                return response()->json(['errors' => ['dt_recisao' => ['Data de recisão não pode ser menor que a data de admissão']]], 406);
            }
        }
        
        $colaborador = Colaborador::where('id', $id)->first();
        $usuario_antigo = $colaborador->usuario;
        $colaborador->matricula = $request->input('matricula');
        $colaborador->nome = $nome;
        $colaborador->email = $request->input('email');
        $colaborador->telefone = $request->input('telefone');
        $colaborador->dt_nascimento = $request->input('dt_nascimento');
        $colaborador->dt_admissao = $request->input('dt_admissao');
        $colaborador->dt_recisao = $request->input('dt_recisao');
        $colaborador->cargo_id = $request->input('cargo_id');
        $colaborador->funcao_id = $request->input('funcao_id');
        $colaborador->usuario = $usuario;
        $colaborador->save();

        if(!$colaborador->id) {
            return response()->json(['errors' => 'Erro ao editar colaborador'], 422);
        }

        //editando usuario
        $usuario = User::where('name', $usuario_antigo)->first();
        if(isset($usuario->name)) {
            $usuario->name = $colaborador->usuario;
            $usuario->save();
        }

        if(!$usuario->id) {
            return response()->json(['errors' => 'Erro ao editar usuario'], 422);
        }

        return response()->json(['colaborador' => $colaborador], 201);
    }

    public function destroy($id)
    {   
        $colaborador = Colaborador::find($id);

        //excluindo usuario
        $usuario = User::where('name', $colaborador->usuario)->first();
        $usuario->delete();

        $colaborador->delete();
        return response()->json(['msg' => 'Colaborador excluído com sucesso'], 200);
    }

    public function consultaCpf(Request $request)
    {
        $colaborador = Colaborador::where('cpf', $request->input('cpf'))->first();

        if(empty($colaborador)) {
            return response(true);
        } else {
            return response(false);
        }
    }

    public function getCargo()
    {
        $cargos = Cargo::all();
        return response()->json(['cargos' => $cargos], 200);
    }

    public function getFuncao()
    {
        $funcoes = Funcao::all();
        return response()->json(['funcoes' => $funcoes], 200);
    }

}
