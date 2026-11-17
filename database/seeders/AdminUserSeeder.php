<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Tipos de personal requeridos
        $tipos = ['Administrador', 'Coordinador Odontologia', 'Encargado'];
        foreach ($tipos as $tipo) {
            $existeTipo = DB::table('tipo_personal')
                ->where('descripcion_per', $tipo)
                ->first();

            if (!$existeTipo) {
                DB::table('tipo_personal')->insert([
                    'descripcion_per' => $tipo,
                ]);
            }
        }

        $tipoAdmin = DB::table('tipo_personal')
            ->where('descripcion_per', 'Administrador')
            ->first();

        // 2. Sede Central
        $sede = DB::table('sede')->where('nombre', 'Sede Central')->first();
        if (!$sede) {
            $idSede = DB::table('sede')->insertGetId([
                'nombre' => 'Sede Central',
            ]);
        } else {
            $idSede = $sede->id_sede;
        }

        // 3. Edificio Central
        $edificio = DB::table('edificio')
            ->where('nombre_edi', 'Edificio Central')
            ->where('SEDE_id_sede', $idSede)
            ->first();

        if (!$edificio) {
            $idEdificio = DB::table('edificio')->insertGetId([
                'nombre_edi' => 'Edificio Central',
                'direccion' => 'Calle Principal',
                'SEDE_id_sede' => $idSede,
            ]);
        } else {
            $idEdificio = $edificio->id_edificio;
        }

        // 4. Personal Administrador
        $personal = DB::table('personal')
            ->where('nombre', 'Administrador')
            ->where('ap_paterno', 'Sistema')
            ->first();

        if (!$personal) {
            $idPersonal = DB::table('personal')->insertGetId([
                'nombre' => 'Administrador',
                'ap_paterno' => 'Sistema',
                'ap_materno' => 'Base',
                'celular' => '00000000',
                'estado' => 1,
                'TIPO_PERSONAL_id_tipo_per' => $tipoAdmin->id_tipo_per,
                'EDIFICIO_id_edificio' => $idEdificio,
            ]);
        } else {
            $idPersonal = $personal->id_personal;
        }

        // 5. Usuario Administrador en tabla login
        $login = DB::table('login')->where('nombre', 'admin')->first();
        if (!$login) {
            DB::table('login')->insert([
                'nombre' => 'admin',
                'contrasena' => Hash::make('admin123*'),
                'PERSONAL_id_personal' => $idPersonal,
            ]);
        } else {
            DB::table('login')->where('nombre', 'admin')->update([
                'contrasena' => Hash::make('admin123*'),
                'PERSONAL_id_personal' => $idPersonal,
            ]);
        }
    }
}