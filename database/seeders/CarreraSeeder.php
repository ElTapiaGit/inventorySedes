<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrera;
use App\Models\Edificio;
use App\Models\Ambiente;
use App\Models\Personal;
use App\Models\TipoAmbiente;
use App\Models\TipoPersonal;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Registro idempotente de carreras
        $odontologia = Carrera::firstOrCreate(
            ['nombre_carrera' => 'Odontologia'],
            [
                'codigo' => 'ODO',
                'logo' => 'img/coordinator/logo_donto.png',
            ]
        );

        $optometria = Carrera::firstOrCreate(
            ['nombre_carrera' => 'Optometria'],
            [
                'codigo' => 'OPT',
                'logo' => null,
            ]
        );

        // 2. Asignacion a Edificio Clinica Odontologia
        Edificio::where('nombre_edi', 'like', '%Clinica Odontologia%')
            ->update(['CARRERA_id_carrera' => $odontologia->id_carrera]);

        // 3. Asignacion a Ambientes Odontologia
        $tiposOdonto = TipoAmbiente::where('nombre_amb', 'like', '%Laboratorio Odontologia%')->pluck('id_tipoambiente');
        if ($tiposOdonto->isNotEmpty()) {
            Ambiente::whereIn('TIPO_AMBIENTE_id_ambiente', $tiposOdonto)
                ->update(['CARRERA_id_carrera' => $odontologia->id_carrera]);
        }

        // 4. Asignacion a Ambientes Optometria
        $tiposOpto = TipoAmbiente::where('nombre_amb', 'like', '%Laboratorio Optometria%')->pluck('id_tipoambiente');
        Ambiente::where(function ($query) use ($tiposOpto) {
            if ($tiposOpto->isNotEmpty()) {
                $query->whereIn('TIPO_AMBIENTE_id_ambiente', $tiposOpto);
            }
            $query->orWhere('nombre', 'like', '%Optica%')
                  ->orWhere('nombre', 'like', '%Optometria%');
        })->update(['CARRERA_id_carrera' => $optometria->id_carrera]);

        // 5. Asignacion a Personal segun tipo_personal
        $tiposPersonalOdonto = TipoPersonal::where('descripcion_per', 'like', '%Odontologia%')->pluck('id_tipo_per');
        if ($tiposPersonalOdonto->isNotEmpty()) {
            Personal::whereIn('TIPO_PERSONAL_id_tipo_per', $tiposPersonalOdonto)
                ->update(['CARRERA_id_carrera' => $odontologia->id_carrera]);
        }

        $tiposPersonalOpto = TipoPersonal::where('descripcion_per', 'like', '%Optometria%')->pluck('id_tipo_per');
        if ($tiposPersonalOpto->isNotEmpty()) {
            Personal::whereIn('TIPO_PERSONAL_id_tipo_per', $tiposPersonalOpto)
                ->update(['CARRERA_id_carrera' => $optometria->id_carrera]);
        }
    }
}
