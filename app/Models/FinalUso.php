<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalUso extends Model
{
    use HasFactory;

    protected $table = 'final_uso';
    protected $primaryKey = 'id_uso_fin';
    public $timestamps = false; // Desactivar los timestamps si no se usan

    protected $fillable = [
        'fch_fin',
        'hora_fin',
        'USO_AMBIENTE_id_uso_ambiente',
        'PERSONAL_id_personal',
    ];

    //realciones
    public function usoAmbiente()
    {
        return $this->belongsTo(UsoAmbiente::class, 'USO_AMBIENTE_id_uso_ambiente');
    }

    public function personal()
    {
        return $this->belongsTo(Personal::class, 'PERSONAL_id_personal');
    }
}
