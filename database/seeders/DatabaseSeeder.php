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
                'coordenadas_vertices' => '41.3456,2.1456;41.3467,2.1467;41.3478,2.1478',
                'descripcion' => 'Principal puerto comercial de España en el Mediterráneo',
                'direccion' => 'Moll de la Costa, s/n, 08039 Barcelona',
                'telefono_contacto' => '+34933295900'
            ],
            [
                'nombre' => 'Puerto de Valencia',
                'codigo' => 'ESVLC',
                'coordenadas_vertices' => '39.4567,-0.3214;39.4578,-0.3225;39.4589,-0.3236',
                'descripcion' => 'Uno de los puertos más importantes del Mediterráneo en tráfico de contenedores',
                'direccion' => 'Avinguda del Muelle del Turia, s/n, 46024 Valencia',
                'telefono_contacto' => '+34962632800'
            ],
            [
                'nombre' => 'Puerto de Algeciras',
                'codigo' => 'ESALG',
                'coordenadas_vertices' => '36.1234,-5.4321;36.1245,-5.4332;36.1256,-5.4343',
                'descripcion' => 'Principal puerto de España en tráfico de mercancías',
                'direccion' => 'Av. de la Hispanidad, s/n, 11207 Algeciras, Cádiz',
                'telefono_contacto' => '+34956068800'
            ]
        ]);
        
        DB::table('tipos_contenedor')->insert([
            [
                'iso_code' => '20GP',
                'descripcion' => 'Contenedor estándar de 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '40GP',
                'descripcion' => 'Contenedor estándar de 40 pies',
                'longitud_m' => 12.19,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '40HQ',
                'descripcion' => 'Contenedor High Cube de 40 pies',
                'longitud_m' => 12.19,
                'anchura_m' => 2.44,
                'altura_m' => 2.90,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '45HQ',
                'descripcion' => 'Contenedor High Cube de 45 pies',
                'longitud_m' => 13.72,
                'anchura_m' => 2.44,
                'altura_m' => 2.90,
                'peso_max_kg' => 32500.00
            ]
        ]);

        // Obtener el ID del Puerto de Barcelona
        $puertoBarcelona = DB::table('puertos')->where('codigo', 'ESBCN')->first();
        $puertoValencia = DB::table('puertos')->where('codigo', 'ESVLC')->first();

        DB::table('zonas')->insert([
            [
                'id_puerto' => $puertoBarcelona->id_puerto,
                'nombre' => 'Zona de contenedores Norte',
                'codigo_zona' => 'BCN-N',
                'coordenadas_vertices' => '41.3456,2.1456;41.3467,2.1467;41.3478,2.1478',
                'espacios_para_contenedores' => 500,
                'max_niveles_apilamiento' => 5,
                'observaciones' => 'Zona para contenedores de importación'
            ],
            [
                'id_puerto' => $puertoBarcelona->id_puerto,
                'nombre' => 'Zona de contenedores Sur',
                'codigo_zona' => 'BCN-S',
                'coordenadas_vertices' => '41.3356,2.1356;41.3367,2.1367;41.3378,2.1378',
                'espacios_para_contenedores' => 750,
                'max_niveles_apilamiento' => 4,
                'observaciones' => 'Zona para contenedores de exportación'
            ],
            [
                'id_puerto' => $puertoValencia->id_puerto,
                'nombre' => 'Terminal de contenedores Este',
                'codigo_zona' => 'VLC-E',
                'coordenadas_vertices' => '39.4567,-0.3214;39.4578,-0.3225;39.4589,-0.3236',
                'espacios_para_contenedores' => 1200,
                'max_niveles_apilamiento' => 6,
                'observaciones' => 'Principal terminal de contenedores del puerto'
            ]
        ]);

        // Obtener IDs de zonas
        $zonaBcnNorte = DB::table('zonas')->where('codigo_zona', 'BCN-N')->first();
        $zonaBcnSur = DB::table('zonas')->where('codigo_zona', 'BCN-S')->first();
        $zonaVlcEste = DB::table('zonas')->where('codigo_zona', 'VLC-E')->first();
        
        DB::table('espacios_contenedores')->insert([
            // Espacios en Zona Norte de Barcelona
            [
                'id_zona' => $zonaBcnNorte->id_zona,
                'nombre' => 'Fila A - Posiciones 1-50',
                'codigo_espacio' => 'BCN-N-A',
                'observaciones' => 'Espacios para contenedores refrigerados'
            ],
            [
                'id_zona' => $zonaBcnNorte->id_zona,
                'nombre' => 'Fila B - Posiciones 1-100',
                'codigo_espacio' => 'BCN-N-B',
                'observaciones' => 'Espacios para contenedores estándar'
            ],
            
            // Espacios en Zona Sur de Barcelona
            [
                'id_zona' => $zonaBcnSur->id_zona,
                'nombre' => 'Fila C - Posiciones 1-150',
                'codigo_espacio' => 'BCN-S-C',
                'observaciones' => 'Espacios para contenedores de exportación'
            ],
            
            // Espacios en Valencia Este
            [
                'id_zona' => $zonaVlcEste->id_zona,
                'nombre' => 'Bloque 1 - Posiciones 1-200',
                'codigo_espacio' => 'VLC-E-1',
                'observaciones' => 'Espacios prioritarios para contenedores High Cube'
            ],
            [
                'id_zona' => $zonaVlcEste->id_zona,
                'nombre' => 'Bloque 2 - Posiciones 1-300',
                'codigo_espacio' => 'VLC-E-2',
                'observaciones' => 'Espacios generales'
            ]
        ]);
        
        // Obtener tipos de contenedor
        $tipo20GP = DB::table('tipos_contenedor')->where('iso_code', '20GP')->first();
        $tipo40GP = DB::table('tipos_contenedor')->where('iso_code', '40GP')->first();
        $tipo40HQ = DB::table('tipos_contenedor')->where('iso_code', '40HQ')->first();
        
        // Obtener espacios de contenedores
        $espacioBcnNA = DB::table('espacios_contenedores')->where('codigo_espacio', 'BCN-N-A')->first();
        $espacioVlcE1 = DB::table('espacios_contenedores')->where('codigo_espacio', 'VLC-E-1')->first();
        
        DB::table('contenedores')->insert([
            [
                'tipo_contenedor_iso' => $tipo20GP->iso_code,
                'espacio_contenedor_id' => $espacioBcnNA->id_espacios_contenedores,
                'propietario' => 'Maersk Line',
                'material_peligroso' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipo_contenedor_iso' => $tipo40GP->iso_code,
                'espacio_contenedor_id' => $espacioBcnNA->id_espacios_contenedores,
                'propietario' => 'MSC',
                'material_peligroso' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipo_contenedor_iso' => $tipo40HQ->iso_code,
                'espacio_contenedor_id' => $espacioVlcE1->id_espacios_contenedores,
                'propietario' => 'COSCO',
                'material_peligroso' => false,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'tipo_contenedor_iso' => $tipo40HQ->iso_code,
                'espacio_contenedor_id' => null, // Contenedor sin ubicación asignada
                'propietario' => 'Evergreen',
                'material_peligroso' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}