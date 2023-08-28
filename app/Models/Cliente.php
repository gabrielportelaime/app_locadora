<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\ClienteRepository;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = ['nome'];
    public function rules(){
        return [
        'nome' => 'required'
        ];
    }
}
