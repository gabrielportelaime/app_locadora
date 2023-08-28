<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Repositories\LocacaoRepository;
use Illuminate\Http\Request;

class LocacaoController extends Controller
{
    public $locacao;
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);
        if($request->has('filtro')){
            $locacaoRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $locacaoRepository->selectAtributos($request->atributos);
        }
        return response()->json($locacaoRepository->getResultado(), 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->locacao->rules());
        $locacao = $this->locacao->create([
            'cliente_id' => $request->cliente_id,
            'carro_id' => $request->carro_id,
            'data_inicio_periodo' => $request->data_inicio_periodo,
            'data_final_previsto_periodo' => $request->data_final_previsto_periodo,
            'data_final_realizado_periodo' => $request->data_final_realizado_periodo,
            'valor_diaria' => $request->valor_diaria,
            'km_inicial' => $request->km_inicial,
            'km_final' => $request->km_final,
        ]);
        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'A locação não foi criada!'], 404);
        }
        return response()->json($locacao, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Locacao $locacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'Não foi possível encontrar a locação!'], 404);
        }
        if($request->method() === 'PATCH'){
            $regras_dinamicas = array();
            foreach($locacao->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas);
        }else{
            $request->validate($locacao->rules());
        }
        //preencher o objeto marca com os dados do request
        $locacao->fill($request->all());
        $locacao->save();
        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $locacao = $this->locacao->find($id);
        if($locacao === null){
            return response()->json(['erro' => 'Impossível remover a locação, pois não existe!'], 404);
        }
        $locacao->delete();
        return response()->json(['msg' => 'A locação foi cancelada com sucesso!'], 200);
    }
}
