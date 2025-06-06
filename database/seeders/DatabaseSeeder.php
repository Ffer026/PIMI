<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed para la tabla puertos (ampliado)
        DB::table('puertos')->insert([
            [
                'nombre' => 'Puerto de Barcelona',
                'codigo' => 'ESBCN',
                'coordenadas_vertices' => '41.3353,2.1047;41.3261,2.1140;41.3149,2.1180;41.2909,2.1475;41.3168,2.1743;41.3265,2.1703;41.3700,2.1925;41.3818,2.1846;41.3522,2.1580',
                'descripcion' => 'Principal puerto comercial de España en el Mediterráneo, con más de 200 hectáreas dedicadas a contenedores.',
                'direccion' => 'Moll de la Costa, s/n, 08039 Barcelona',
                'telefono_contacto' => '+34933295900',
                'fecha_creacion' => Carbon::create(2015, 1, 15),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'nombre' => 'Puerto de Valencia',
                'codigo' => 'ESVLC',
                'coordenadas_vertices' => '39.4628,-0.3292;39.4627,-0.3081;39.4552,-0.2851;39.4442,-0.2915;39.4334,-0.3025;39.4226,-0.3129;39.4205,-0.3176;39.4259,-0.3327;39.4379,-0.3345;39.4469,-0.3266;39.4553,-0.3291;39.4598,-0.3322',
                'descripcion' => 'Primer puerto de España en tráfico de contenedores y uno de los más importantes del Mediterráneo.',
                'direccion' => 'Avinguda del Muelle del Turia, s/n, 46024 Valencia',
                'telefono_contacto' => '+34962632800',
                'fecha_creacion' => Carbon::create(2016, 3, 22),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'nombre' => 'Puerto de Algeciras',
                'codigo' => 'ESALG',
                'coordenadas_vertices' => '36.1162,-5.4356;36.1183,-5.4418;36.1212,-5.4416;36.1234,-5.4403;36.1317,-5.4456;36.1342,-5.4454;36.1377,-5.4459;36.1476,-5.4417;36.1488,-5.4409;36.1484,-5.4356;36.1477,-5.4282;36.1476,-5.4279;36.1384,-5.4257;36.1329,-5.4227;36.1199,-5.4223;36.1202,-5.4323',
                'descripcion' => 'Principal puerto de España en tráfico de mercancías y uno de los más importantes de Europa.',
                'direccion' => 'Av. de la Hispanidad, s/n, 11207 Algeciras, Cádiz',
                'telefono_contacto' => '+34956068800',
                'fecha_creacion' => Carbon::create(2017, 5, 10),
                'ultima_actualizacion' => Carbon::now()
            ]
        ]);
        
        // Seed para tipos_contenedor (ampliado con más tipos reales)
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
            ],
            [
                'iso_code' => '20RF',
                'descripcion' => 'Contenedor refrigerado de 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '40RH',
                'descripcion' => 'Contenedor refrigerado High Cube de 40 pies',
                'longitud_m' => 12.19,
                'anchura_m' => 2.44,
                'altura_m' => 2.90,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '20TK',
                'descripcion' => 'Contenedor tanque de 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 30480.00
            ],
            [
                'iso_code' => '20OT',
                'descripcion' => 'Contenedor Open Top de 20 pies',
                'longitud_m' => 6.06,
                'anchura_m' => 2.44,
                'altura_m' => 2.59,
                'peso_max_kg' => 28000.00
            ]
        ]);

        // Obtener los IDs de los puertos
        $puertoBarcelona = DB::table('puertos')->where('codigo', 'ESBCN')->first();
        $puertoValencia = DB::table('puertos')->where('codigo', 'ESVLC')->first();
        $puertoAlgeciras = DB::table('puertos')->where('codigo', 'ESALG')->first();

        // Insertar zonas para cada puerto (más detalladas)
        DB::table('zonas')->insert([
            // Zonas para Barcelona
            [
                'id_puerto' => $puertoBarcelona->id_puerto,
                'nombre' => 'Terminal de Contenedores Norte',
                'codigo_zona' => 'BCN-N',
                'coordenadas_vertices' => '41.3300,2.1496;41.3416,2.1568;41.3422,2.1581;41.3412,2.1647;41.3284,2.1590;41.3284,2.1539',
                'espacios_para_contenedores' => 850,
                'max_niveles_apilamiento' => 5,
                'observaciones' => 'Zona para contenedores de importación y tránsito',
                'fecha_creacion' => Carbon::create(2018, 4, 12),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_puerto' => $puertoBarcelona->id_puerto,
                'nombre' => 'Terminal de Contenedores Sur',
                'codigo_zona' => 'BCN-S',
                'coordenadas_vertices' => '41.3208,2.1164;41.3131,2.1210;41.3044,2.1335;41.3181,2.1474;41.3202,2.1324;41.3222,2.1179',
                'espacios_para_contenedores' => 1200,
                'max_niveles_apilamiento' => 6,
                'observaciones' => 'Zona principal para contenedores de exportación',
                'fecha_creacion' => Carbon::create(2019, 6, 5),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_puerto' => $puertoBarcelona->id_puerto,
                'nombre' => 'Terminal de Contenedores Especiales',
                'codigo_zona' => 'BCN-E',
                'coordenadas_vertices' => '41.3214,2.1506;41.3247,2.1159;41.3295,2.1109;41.3366,2.1121;41.3524,2.1572;41.3594,2.1660;41.3565,2.1731;41.3472,2.1670;41.3484,2.1635;41.3417,2.1503;41.3367,2.1497;41.3310,2.1459;41.3300,2.1489;41.3280,2.1538;41.3218,2.1515',
                'espacios_para_contenedores' => 300,
                'max_niveles_apilamiento' => 3,
                'observaciones' => 'Zona para contenedores refrigerados, peligrosos y especiales',
                'fecha_creacion' => Carbon::create(2020, 3, 15),
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Zonas para Valencia
            [
                'id_puerto' => $puertoValencia->id_puerto,
                'nombre' => 'Terminal de Contenedores Este',
                'codigo_zona' => 'VLC-E',
                'coordenadas_vertices' => '39.4581,-0.3226;39.4591,-0.3187;39.4501,-0.3123;39.4462,-0.3144',
                'espacios_para_contenedores' => 2500,
                'max_niveles_apilamiento' => 7,
                'observaciones' => 'Principal terminal de contenedores del puerto con capacidad para mega buques',
                'fecha_creacion' => Carbon::create(2017, 11, 20),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_puerto' => $puertoValencia->id_puerto,
                'nombre' => 'Terminal de Contenedores Oeste',
                'codigo_zona' => 'VLC-O',
                'coordenadas_vertices' => '39.4435,-0.3160;39.4464,-0.3264;39.4373,-0.3340;39.4278,-0.3279;39.4227,-0.3130;39.4308,-0.3090;39.4355,-0.3135',
                'espacios_para_contenedores' => 1800,
                'max_niveles_apilamiento' => 6,
                'observaciones' => 'Terminal secundaria para contenedores con conexión directa a vías férreas',
                'fecha_creacion' => Carbon::create(2018, 9, 10),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_puerto' => $puertoValencia->id_puerto,
                'nombre' => 'Terminal de Contenedores Refrigerados',
                'codigo_zona' => 'VLC-R',
                'coordenadas_vertices' => '39.4510,-0.3085;39.4493,-0.3020;39.4336,-0.3022;39.4357,-0.3060;39.4446,-0.3053;39.4447,-0.3087;39.4422,-0.3103;39.4419,-0.3127;39.4429,-0.3130',
                'espacios_para_contenedores' => 600,
                'max_niveles_apilamiento' => 3,
                'observaciones' => 'Zona especializada para contenedores refrigerados con 1000 conexiones eléctricas',
                'fecha_creacion' => Carbon::create(2019, 5, 22),
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Zonas para Algeciras
            [
                'id_puerto' => $puertoAlgeciras->id_puerto,
                'nombre' => 'Terminal de Contenedores Norte',
                'codigo_zona' => 'ALG-N',
                'coordenadas_vertices' => '36.1488,-5.4409;36.1483,-5.4356;36.1349,-5.4362;36.1328,-5.4397;36.1303,-5.4373;36.1278,-5.4427;36.1304,-5.4444;36.1311,-5.4429;36.1372,-5.4418;36.1467,-5.4414;36.1476,-5.4417;36.1481,-5.4410',
                'espacios_para_contenedores' => 2000,
                'max_niveles_apilamiento' => 6,
                'observaciones' => 'Principal zona de almacenamiento de contenedores con acceso directo a terminal ferroviaria',
                'fecha_creacion' => Carbon::create(2018, 7, 14),
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_puerto' => $puertoAlgeciras->id_puerto,
                'nombre' => 'Terminal de Contenedores Sur',
                'codigo_zona' => 'ALG-S',
                'coordenadas_vertices' => '36.1271,-5.4411;36.1280,-5.4319;36.1330,-5.4309;36.1329,-5.4228;36.1205,-5.4223;36.1202,-5.4330;36.1207,-5.4355;36.1218,-5.4347;36.1227,-5.4360;36.1214,-5.4370;36.1213,-5.4416;36.1237,-5.4402',
                'espacios_para_contenedores' => 1500,
                'max_niveles_apilamiento' => 5,
                'observaciones' => 'Zona para contenedores de exportación y tránsito hacia África',
                'fecha_creacion' => Carbon::create(2019, 2, 28),
                'ultima_actualizacion' => Carbon::now()
            ]
        ]);

        // Obtener IDs de todas las zonas
        $zonas = DB::table('zonas')->get()->keyBy('codigo_zona');
        
        // Seed para espacios_contenedores (más detallado)
        DB::table('espacios_contenedores')->insert([
            // Espacios en Terminal Norte de Barcelona
            [
                'id_zona' => $zonas['BCN-N']->id_zona,
                'nombre' => 'Fila A - Posiciones 1-100',
                'codigo_espacio' => 'BCN-N-A1',
                'observaciones' => 'Espacios para contenedores refrigerados (100 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['BCN-N']->id_zona,
                'nombre' => 'Fila B - Posiciones 1-250',
                'codigo_espacio' => 'BCN-N-B1',
                'observaciones' => 'Espacios para contenedores estándar 40\' (250 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['BCN-N']->id_zona,
                'nombre' => 'Fila C - Posiciones 1-200',
                'codigo_espacio' => 'BCN-N-C1',
                'observaciones' => 'Espacios para contenedores High Cube (200 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Terminal Sur de Barcelona
            [
                'id_zona' => $zonas['BCN-S']->id_zona,
                'nombre' => 'Bloque 1 - Posiciones 1-300',
                'codigo_espacio' => 'BCN-S-1',
                'observaciones' => 'Espacios prioritarios para exportación (300 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['BCN-S']->id_zona,
                'nombre' => 'Bloque 2 - Posiciones 1-400',
                'codigo_espacio' => 'BCN-S-2',
                'observaciones' => 'Espacios generales para exportación (400 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Terminal Este de Barcelona
            [
                'id_zona' => $zonas['BCN-E']->id_zona,
                'nombre' => 'Área Refrigerados - Posiciones 1-100',
                'codigo_espacio' => 'BCN-E-RF',
                'observaciones' => 'Espacios con conexión eléctrica para refrigerados (100 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['BCN-E']->id_zona,
                'nombre' => 'Área Peligrosos - Posiciones 1-50',
                'codigo_espacio' => 'BCN-E-DG',
                'observaciones' => 'Espacios aislados para contenedores de materiales peligrosos (50 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Valencia Este
            [
                'id_zona' => $zonas['VLC-E']->id_zona,
                'nombre' => 'Bloque 1 - Posiciones 1-500',
                'codigo_espacio' => 'VLC-E-1',
                'observaciones' => 'Espacios prioritarios para contenedores High Cube (500 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['VLC-E']->id_zona,
                'nombre' => 'Bloque 2 - Posiciones 1-800',
                'codigo_espacio' => 'VLC-E-2',
                'observaciones' => 'Espacios generales para contenedores estándar (800 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Valencia Oeste
            [
                'id_zona' => $zonas['VLC-O']->id_zona,
                'nombre' => 'Bloque 3 - Posiciones 1-600',
                'codigo_espacio' => 'VLC-O-3',
                'observaciones' => 'Espacios para contenedores estándar con acceso ferroviario (600 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Valencia Refrigerados
            [
                'id_zona' => $zonas['VLC-R']->id_zona,
                'nombre' => 'Bloque R - Posiciones 1-200',
                'codigo_espacio' => 'VLC-R-R1',
                'observaciones' => 'Espacios con conexión eléctrica para refrigerados (200 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Algeciras Norte
            [
                'id_zona' => $zonas['ALG-N']->id_zona,
                'nombre' => 'Área 1 - Posiciones 1-500',
                'codigo_espacio' => 'ALG-N-1',
                'observaciones' => 'Espacios principales de almacenamiento (500 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            [
                'id_zona' => $zonas['ALG-N']->id_zona,
                'nombre' => 'Área 2 - Posiciones 1-500',
                'codigo_espacio' => 'ALG-N-2',
                'observaciones' => 'Espacios para tránsito rápido (500 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ],
            
            // Espacios en Algeciras Sur
            [
                'id_zona' => $zonas['ALG-S']->id_zona,
                'nombre' => 'Área 3 - Posiciones 1-400',
                'codigo_espacio' => 'ALG-S-3',
                'observaciones' => 'Espacios para exportación a África (400 posiciones)',
                'ultima_actualizacion' => Carbon::now()
            ]
        ]);

        // Obtener todos los tipos de contenedor
        $tiposContenedor = DB::table('tipos_contenedor')->get()->keyBy('iso_code');
        
        // Obtener espacios de contenedores
        $espacios = DB::table('espacios_contenedores')->get()->keyBy('codigo_espacio');
        
        // Seed para contenedores (más realista con variedad de tipos y ubicaciones)
        DB::table('contenedores')->insert([
            // Contenedores en Barcelona Norte
            [
                'tipo_contenedor_iso' => $tiposContenedor['20GP']->iso_code,
                'espacio_contenedor_id' => $espacios['BCN-N-A1']->id_espacios_contenedores,
                'propietario' => 'Maersk Line',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()
            ],
            [
                'tipo_contenedor_iso' => $tiposContenedor['40GP']->iso_code,
                'espacio_contenedor_id' => $espacios['BCN-N-B1']->id_espacios_contenedores,
                'propietario' => 'MSC',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()
            ],
            [
                'tipo_contenedor_iso' => $tiposContenedor['40HQ']->iso_code,
                'espacio_contenedor_id' => $espacios['BCN-N-C1']->id_espacios_contenedores,
                'propietario' => 'CMA CGM',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()
            ],
            [
                'tipo_contenedor_iso' => $tiposContenedor['20RF']->iso_code,
                'espacio_contenedor_id' => $espacios['BCN-E-RF']->id_espacios_contenedores,
                'propietario' => 'Seatrade',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()
            ],
            [
                'tipo_contenedor_iso' => $tiposContenedor['40GP']->iso_code,
                'espacio_contenedor_id' => $espacios['BCN-E-DG']->id_espacios_contenedores,
                'propietario' => 'Hapag-Lloyd',
                'material_peligroso' => true,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()
            ],
            
            // Contenedores en Valencia Este
            [
                'tipo_contenedor_iso' => $tiposContenedor['45HQ']->iso_code,
                'espacio_contenedor_id' => $espacios['VLC-E-1']->id_espacios_contenedores,
                'propietario' => 'COSCO',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()
            ],
            [
                'tipo_contenedor_iso' => $tiposContenedor['40RH']->iso_code,
                'espacio_contenedor_id' => $espacios['VLC-R-R1']->id_espacios_contenedores,
                'propietario' => 'MSC',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()
            ],
            
            // Contenedores en Algeciras Norte
            [
                'tipo_contenedor_iso' => $tiposContenedor['40HQ']->iso_code,
                'espacio_contenedor_id' => $espacios['ALG-N-1']->id_espacios_contenedores,
                'propietario' => 'Evergreen',
                'material_peligroso' => false,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()
            ]
        ]);
        \App\Models\Contenedor::factory(500)->create();
    }
}