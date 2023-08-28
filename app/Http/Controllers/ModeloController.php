<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Repositories\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModeloController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public Modelo $modelo;
    public function __construct(Modelo $modelo){
        $this->modelo = $modelo;
    }
    public function index(Request $request)
    {
        $modeloRepository = new ModeloRepository($this->modelo);
        if($request->has('atributos_marca')){
            $atributos_marca = 'marca:id,'.$request->atributos_marca;
            $modeloRepository->selectAtributosRegistrosRelacionados($atributos_marca);
        }else{
            $modeloRepository->selectAtributosRegistrosRelacionados('marca');
        }
        if($request->has('filtro')){
            $modeloRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $modeloRepository->selectAtributos($request->atributos);
        }
        return response()->json($modeloRepository->getResultado(), 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate($this->modelo->rules());
        //stateless
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');
        $modelo = $this->modelo->create([
            'marca_id' => $request->marca_id,
            'nome' => $request->nome,
            'imagem' => $imagem_urn,
            'numero_portas' => $request->numero_portas,
            'lugares' => $request->lugares,
            'air_bag' => $request->air_bag,
            'abs' => $request->abs,
        ]);
        return response()->json($modelo, 201);
    }

    public function show($id)
    {
        $modelo = $this->modelo->with('marca')->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'A modelo não foi encontrado!'], 404);
        }
        return response()->json($modelo, 200);
    }

    public function edit(Modelo $modelo)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Não foi possível encontrar o modelo!'], 404);
        }
        if($request->method() === 'PATCH'){
            $regras_dinamicas = array();
            foreach($modelo->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas);
        }else{
            $request->validate($modelo->rules());
        }

        if($request->file('imagem')){
            Storage::disk('public')->delete($modelo->imagem);
        }

        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/modelos', 'public');
        $modelo->fill($request->all());
        $modelo->imagem = $imagem_urn;
        $modelo->save();
        return response()->json($modelo, 200);
    }

    public function destroy($id)
    {
        $modelo = $this->modelo->find($id);
        if($modelo === null){
            return response()->json(['erro' => 'Impossível remover o modelo, pois não existe!'], 404);
        }
        Storage::disk('public')->delete($modelo->imagem);
        $modelo->delete();
        return response()->json(['msg' => 'O modelo foi removido com sucesso!'], 200);
    }
}
