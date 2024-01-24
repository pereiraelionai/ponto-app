<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feriado;

class FeriadosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getView()
    {
        return view('feriados');
    }

    // Rotas API
    // -----------------------------------------------------------------------------------

    public function index()
    {   
        $feriados = Feriado::orderByDesc('id')->paginate(10);
        return response()->json(['feriados' => $feriados]);
    }

    public function store(Request $request) 
    {   
        $regras = [
            'descricao' => 'required|max:255',
            'data' => 'required|date|unique:feriados,data'
        ];

        $msg = [
            'required' => 'O :attribute é obrigatório',
            'date' => 'Formato de data inválido',
            'max' => 'Tamanho máximo excedido',
            'unique' => 'Data já cadastrada'
        ];

        $request->validate($regras, $msg);

        $feriado = new Feriado();
        $feriado->data = $request->input('data');
        $feriado->descricao = $request->input('descricao');
        $feriado->save();

        if(!$feriado->id) {
            return response()->json(['errors' => 'Erro ao salvar feriado'], 422);
        }

        return response()->json(['feriado' => $feriado], 201);

    }

    public function update(Request $request, $id) 
    {   
        $feriado = Feriado::find($id);
        
        $regras = [
            'descricao' => 'required|max:255',
        ];

        if($feriado->data != $request->input('data')) {
            $regras['data'] = 'required|date|unique:feriados,data';
        } else {
            $regras['data'] = 'required|date';
        }

        $msg = [
            'required' => 'O :attribute é obrigatório',
            'date' => 'Formato de data inválido',
            'max' => 'Tamanho máximo excedido',
            'unique' => 'Data já cadastrada'
        ];

        $request->validate($regras, $msg);

        $feriado->data = $request->input('data');
        $feriado->descricao = $request->input('descricao');
        $feriado->save();

        if(!$feriado->id) {
            return response()->json(['errors' => 'Erro ao editar feriado'], 422);
        }

        return response()->json(['feriado' => $feriado], 201);

    }    

    public function destroy($id)
    {   
        $feriado = Feriado::find($id);
        $feriado->delete();
        return response()->json(['msg' => 'Feriado excluído com sucesso'], 200);
    }    
}
