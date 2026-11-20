<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Personal;

class PersonalEdificioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $personales = Personal::whereNotNull('EDIFICIO_id_edificio')->get();

        foreach ($personales as $persona) {
            DB::table('personal_edificio')->updateOrInsert(
                [
                    'PERSONAL_id_personal' => $persona->id_personal,
                    'EDIFICIO_id_edificio' => $persona->EDIFICIO_id_edificio,
                ],
                [
                    'es_principal' => 1,
                ]
            );
        }
    }
}
