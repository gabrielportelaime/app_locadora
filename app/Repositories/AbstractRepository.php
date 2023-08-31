<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository{
    public $model;
    public function __construct($model){
        $this->model = $model;
    }
    public function selectAtributosRegistrosRelacionados($atributos){
        $this->model = $this->model->with($atributos);

    }
    public function filtro($filtros){
        $filtros = explode(';', $filtros);
        foreach($filtros as $condicao){
            $filtro = explode(':', $condicao);
            $this->model = $this->model->where($filtro[0], $filtro[1], $filtro[2]);
        }
    }
    public function selectAtributos($atributos){
        $this->model = $this->model->selectRaw($atributos);
    }
    public function getResultado(){
        return $this->model->get();
    }
    public function getResultadoPaginado($numeroRegistrosPorPagina){
        return $this->model->paginate($numeroRegistrosPorPagina);
    }
}
?>