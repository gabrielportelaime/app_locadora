<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public Cliente $cliente;
    public function __construct(Cliente $cliente){
        $this->cliente = $cliente;
    }
    public function index(Request $request)
    {
        $clienteRepository = new ClienteRepository($this->cliente);
        if($request->has('filtro')){
            $clienteRepository->filtro($request->filtro);
        }
        if($request->has('atributos')){
            $clienteRepository->selectAtributos($request->atributos);
        }
        return response()->json($clienteRepository->getResultado(), 200);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->cliente->rules());
        $cliente = $this->cliente->create([
            'nome' => $request->nome
        ]);
        return response()->json($cliente, 201);
    }

    public function show($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json(['erro' => 'O cliente não foi encontrado!'], 404);
        }
        return response()->json($cliente, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json(['erro' => 'Não foi possível encontrar o cliente!'], 404);
        }
        if($request->method() === 'PATCH'){
            $regras_dinamicas = array();
            foreach($cliente->rules() as $input => $regra){
                if(array_key_exists($input, $request->all())){
                    $regras_dinamicas[$input] = $regra;
                }
            }
            $request->validate($regras_dinamicas);
        }else{
            $request->validate($cliente->rules());
        }
        //preencher o objeto marca com os dados do request
        $cliente->fill($request->all());
        $cliente->save();
        return response()->json($cliente, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cliente = $this->cliente->find($id);
        if($cliente === null){
            return response()->json(['erro' => 'Impossível remover o cliente, pois não existe!'], 404);
        }
        $cliente->delete();
        return response()->json(['msg' => 'O cliente foi removido com sucesso!'], 200);
    }
}
