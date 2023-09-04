<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use App\Repositories\MarcaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MarcaController extends Controller
{

    public Marca $marca;
    public function __construct(Marca $marca){
        $this->marca = $marca;
    }
    public function index(Request $request)
    {
        $marcaRepository = new MarcaRepository($this->marca);
        if($request->has('atributos_modelos')){
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        }else{
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }
        if($request->has('filtro')){
            $marcaRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $marcaRepository->selectAtributos($request->atributos);
        }
        return response()->json($marcaRepository->getResultadoPaginado(5), 200);
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $request->validate($this->marca->rules(), $this->marca->feedbacks());
        //stateless
        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens/marcas', 'public');
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        return response()->json($marca, 201);
    }
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if($marca === null){
            return response()->json(['erro' => 'A marca não foi encontrada!'], 404);
        }
        return response()->json($marca, 200);
    }
    public function edit(Marca $marca)
    {
        //
    }
    public function update(Request $request, $id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Não foi possível encontrar a marca!'], 404);
        }
        if($request->method() === 'PATCH'){
            $regras_dinamicas = array();
            foreach($marca->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas, $marca->feedbacks());
        }else{
            $request->validate($marca->rules(), $marca->feedbacks());
        }
        $marca->fill($request->all());
        if($request->file('imagem')){
            Storage::disk('public')->delete($marca->imagem);
            $imagem = $request->file('imagem');
            $imagem_urn = $imagem->store('imagens/marcas', 'public');
            $marca->imagem = $imagem_urn;
        }
        $marca->save();
        return response()->json($marca, 200);
    }
    public function destroy($id)
    {
        $marca = $this->marca->find($id);
        if($marca === null){
            return response()->json(['erro' => 'Impossível remover a marca, pois não existe!'], 404);
        }
        Storage::disk('public')->delete($marca->imagem);
        $marca->delete();
        return response()->json(['msg' => 'A marca foi removida com sucesso!'], 200);
    }
}
