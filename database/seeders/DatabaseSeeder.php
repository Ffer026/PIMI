<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed para la tabla puertos
        DB::table('puertos')->insert([
            [
                'nombre' => 'Puerto de Barcelona',
                'codigo' => 'ESBCN',
                'coordenadas_poligono' => '41.3478,2.1456;41.3489,2.1478;41.3495,2.1492;41.3501,2.1513;41.3490,2.1534;41.3478,2.1456',
                'descripcion' => 'Principal puerto comercial de España en el Mediterráneo',
                'direccion' => 'Moll de la Costa, s/n, 08039 Barcelona',
                'telefono_contacto' => '+3493298590',
                'activo' => true
            ],
            [
                'nombre' => 'Puerto de Valencia',
                'codigo' => 'ESVLC',
                'coordenadas_poligono' => '39.4556,-0.3167;39.4568,-0.3189;39.4575,-0.3202;39.4581,-0.3223;39.4570,-0.3244;39.4556,-0.3167',
                'descripcion' => 'Puerto más activo de España en tráfico de contenedores',
                'direccion' => 'Muelle de la Aduana, s/n, 46024 Valencia',
                'telefono_contacto' => '+3496273280',
                'activo' => true
            ],
            [
                'nombre' => 'Puerto de Algeciras',
                'codigo' => 'ESALG',
                'coordenadas_poligono' => '36.1278,-5.4433;36.1289,-5.4455;36.1295,-5.4468;36.1301,-5.4489;36.1290,-5.4510;36.1278,-5.4433',
                'descripcion' => 'Principal puerto del Estrecho de Gibraltar y uno de los más importantes de Europa',
                'direccion' => 'Av. de la Hispanidad, s/n, 11207 Algeciras',
                'telefono_contacto' => '+3495606840',
                'activo' => true
            ]
        ]);

        // Seed para la tabla tipos_contenedor
        DB::table('tipos_contenedor')->insert([
            [
                'iso_code' => '20GP',
                'descripcion' => 'Contenedor estándar 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '40GP',
                'descripcion' => 'Contenedor estándar 40 pies',
                'longitud_m' => 12.19,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '40HQ',
                'descripcion' => 'Contenedor High Cube 40 pies',
                'longitud_m' => 12.19,
                'anchura_m' => 2.44,
                'altura_m' => 2.90,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '45HQ',
                'descripcion' => 'Contenedor High Cube 45 pies',
                'longitud_m' => 13.72,
                'anchura_m' => 2.44,
                'altura_m' => 2.90,
                'peso_max_kg' => 32500.00
            ],
            [
                'iso_code' => '20TK',
                'descripcion' => 'Contenedor tanque 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ]
        ]);

        // Seed para la tabla zonas
        DB::table('zonas')->insert([
            [
                'id_puerto' => 1, // Puerto de Barcelona
                'nombre' => 'Zona A - Terminal Norte',
                'codigo_zona' => 'BARC-A',
                'esquina_superior_izq' => '41.3478,2.1456',
                'inclinacion_grados' => 0.00,
                'filas' => 20,
                'contenedores_por_fila' => 10,
                'longitud_contenedores' => 40,
                'separacion_entre_filas' => 2.50,
                'max_niveles_apilamiento' => 5,
                'activa' => true,
                'observaciones' => 'Zona para contenedores de importación'
            ],
            [
                'id_puerto' => 1, // Puerto de Barcelona
                'nombre' => 'Zona B - Terminal Sur',
                'codigo_zona' => 'BARC-B',
                'esquina_superior_izq' => '41.3460,2.1430',
                'inclinacion_grados' => 1.50,
                'filas' => 15,
                'contenedores_por_fila' => 12,
                'longitud_contenedores' => 20,
                'separacion_entre_filas' => 2.00,
                'max_niveles_apilamiento' => 4,
                'activa' => true,
                'observaciones' => 'Zona para contenedores de exportación'
            ],
            [
                'id_puerto' => 2, // Puerto de Valencia
                'nombre' => 'Zona 1 - Terminal Principal',
                'codigo_zona' => 'VAL-1',
                'esquina_superior_izq' => '39.4556,-0.3167',
                'inclinacion_grados' => 0.50,
                'filas' => 30,
                'contenedores_por_fila' => 15,
                'longitud_contenedores' => 40,
                'separacion_entre_filas' => 3.00,
                'max_niveles_apilamiento' => 6,
                'activa' => true,
                'observaciones' => 'Zona principal de almacenamiento'
            ],
            [
                'id_puerto' => 3, // Puerto de Algeciras
                'nombre' => 'Zona Este - Terminal de Transbordo',
                'codigo_zona' => 'ALG-E',
                'esquina_superior_izq' => '36.1278,-5.4433',
                'inclinacion_grados' => 0.00,
                'filas' => 25,
                'contenedores_por_fila' => 20,
                'longitud_contenedores' => 40,
                'separacion_entre_filas' => 2.80,
                'max_niveles_apilamiento' => 5,
                'activa' => true,
                'observaciones' => 'Zona para contenedores en tránsito'
            ]
        ]);

        // Seed para la tabla contenedores
        DB::table('contenedores')->insert([
            [
                'id_contenedor' => 'MSKU1234567',
                'id_zona' => 1,
                'iso_code' => '40HQ',
                'fila' => 5,
                'posicion' => 3,
                'nivel' => 2,
                'estado' => 'lleno',
                'peso_actual_kg' => 24500.50,
                'es_peligroso' => false
            ],
            [
                'id_contenedor' => 'TGHU7654321',
                'id_zona' => 1,
                'iso_code' => '40GP',
                'fila' => 5,
                'posicion' => 3,
                'nivel' => 1,
                'estado' => 'vacio',
                'peso_actual_kg' => null,
                'es_peligroso' => false
            ],
            [
                'id_contenedor' => 'APHU9876543',
                'id_zona' => 2,
                'iso_code' => '20GP',
                'fila' => 8,
                'posicion' => 2,
                'nivel' => 1,
                'estado' => 'lleno',
                'peso_actual_kg' => 18500.75,
                'es_peligroso' => true
            ],
            [
                'id_contenedor' => 'CAIU4567890',
                'id_zona' => 3,
                'iso_code' => '45HQ',
                'fila' => 12,
                'posicion' => 5,
                'nivel' => 3,
                'estado' => 'en_transito',
                'peso_actual_kg' => 28900.25,
                'es_peligroso' => false
            ],
            [
                'id_contenedor' => 'BMOU3210987',
                'id_zona' => 4,
                'iso_code' => '40HQ',
                'fila' => 7,
                'posicion' => 9,
                'nivel' => 2,
                'estado' => 'inspeccion',
                'peso_actual_kg' => 21000.00,
                'es_peligroso' => false
            ]
        ]);
    }
}