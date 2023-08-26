<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;
    protected $fillable = ['nome', 'imagem'];
    public function rules(){
        return [
            'nome' => 'required|unique:marcas,nome,'.$this->id.'|min:3|max:10',
            'imagem' => 'required|file|mimes:png,jpeg',
        ];
    }
    public function feedbacks(){
        return [
            'required' => 'O campo :attribute deve ser preenchido',
            'nome.unique' => 'Já existe uma marca com esse nome',
            'nome.min' => 'O nome deve ter pelo menos 3 caracteres',
            'nome.max' => 'O nome deve ter no máximo 10 caracteres',
            'imagem.mimes' => 'O arquivo deve ser imagem do tipo png ou jpeg'
        ];
    }
}
