<?php

namespace Database\Seeders;

use App\Models\Bono;
use App\Models\BonoConsumo;
use App\Models\BonoPlantilla;
use App\Models\Cabina;
use App\Models\CategoriaProducto;
use App\Models\CategoriaTratamiento;
use App\Models\Cita;
use App\Models\CitaServicio;
use App\Models\Cliente;
use App\Models\MovimientoStock;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Tratamiento;
use App\Models\User;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\VentaPago;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $this->seedCabinas();
        $this->seedEmpleados();
        $this->seedCategoriasTratamientos();
        $this->seedTratamientos();
        $this->seedProveedores();
        $this->seedCategoriasProductos();
        $this->seedProductos();
        $this->seedClientes();
        $this->seedBonosPlantillas();
        $this->seedCitas();
        $this->seedBonos();
        $this->seedVentas();
        $this->seedMovimientosStock();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /* ========================================================
     | CABINAS
     |========================================================*/
    protected function seedCabinas(): void
    {
        $cabinas = [
            ['Cabina Rosa',     'Cabina principal de tratamientos faciales',  '#d4a5c0'],
            ['Cabina Lavanda',  'Cabina de masajes relajantes',                '#9b87a8'],
            ['Cabina Dorada',   'Cabina premium con todos los servicios',      '#c9a86a'],
            ['Cabina Jade',     'Cabina de tratamientos corporales',           '#7fb685'],
            ['Cabina Coral',    'Cabina de depilación',                        '#e9a86a'],
            ['Cabina Perla',    'Cabina de manicura y pedicura',               '#c8b8c4'],
            ['Cabina Aqua',     'Cabina de hidroterapia',                      '#7aa9c7'],
            ['Cabina Esmeralda','Cabina de aromaterapia',                      '#5fa06b'],
            ['Cabina Marfil',   'Cabina de tratamiento capilar',               '#e8d9c4'],
            ['Cabina Rubí',     'Suite VIP',                                   '#b04848'],
        ];

        foreach ($cabinas as [$n, $d, $c]) {
            Cabina::firstOrCreate(['nombre' => $n], ['descripcion' => $d, 'color' => $c, 'activo' => true]);
        }
    }

    /* ========================================================
     | EMPLEADOS (usuarios)
     |========================================================*/
    protected function seedEmpleados(): void
    {
        $empleados = [
            ['María Fernanda López',   'maria@estetica.local',   'profesional123',  'profesional'],
            ['Sofía González',         'sofia@estetica.local',   'profesional123',  'profesional'],
            ['Lucía Ramírez',          'lucia@estetica.local',   'profesional123',  'profesional'],
            ['Andrea Martínez',        'andrea@estetica.local',  'profesional123',  'profesional'],
            ['Valeria Hernández',      'valeria@estetica.local', 'profesional123',  'profesional'],
            ['Camila Torres',          'camila@estetica.local',  'profesional123',  'profesional'],
            ['Isabella Rojas',         'isabella@estetica.local','profesional123',  'profesional'],
            ['Daniela Vargas',         'daniela@estetica.local', 'profesional123',  'profesional'],
            ['Carolina Méndez',        'carolina@estetica.local','recepcionista123','recepcionista'],
            ['Patricia Aguilar',       'patricia@estetica.local','cajero123',       'cajero'],
        ];

        foreach ($empleados as [$nombre, $email, $pass, $rol]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name'     => $nombre,
                    'password' => Hash::make($pass),
                    'rol'      => $rol,
                    'telefono' => '+502 ' . random_int(2000, 7999) . '-' . random_int(1000, 9999),
                    'activo'   => true,
                ]
            );
        }
    }

    /* ========================================================
     | CATEGORIAS TRATAMIENTOS + TRATAMIENTOS
     |========================================================*/
    protected function seedCategoriasTratamientos(): void
    {
        $cats = [
            ['Faciales',        '#d4a5c0', 'flower2'],
            ['Corporales',      '#c9a86a', 'gem'],
            ['Masajes',         '#9b87a8', 'hand-thumbs-up'],
            ['Depilación',      '#e9a86a', 'scissors'],
            ['Manicura',        '#7fb685', 'hand-index-thumb'],
            ['Pedicura',        '#7aa9c7', 'water'],
            ['Capilar',         '#b89058', 'palette'],
            ['Maquillaje',      '#b04848', 'palette2'],
            ['Pestañas y cejas','#5d3f5e', 'eye'],
            ['Spa premium',     '#c9a86a', 'stars'],
        ];
        foreach ($cats as [$n, $c, $i]) {
            CategoriaTratamiento::firstOrCreate(['nombre' => $n], ['color' => $c, 'icono' => $i, 'activo' => true]);
        }
    }

    protected function seedTratamientos(): void
    {
        $tratamientos = [
            // [categoria, nombre, duracion, precio, comision]
            ['Faciales',        'Limpieza facial profunda',       60, 350, 15],
            ['Faciales',        'Hidratación facial premium',     75, 450, 15],
            ['Faciales',        'Antiedad con colágeno',          90, 650, 18],
            ['Corporales',      'Exfoliación corporal completa',  60, 400, 15],
            ['Corporales',      'Envoltura de algas',             90, 550, 18],
            ['Masajes',         'Masaje relajante 60 min',        60, 380, 20],
            ['Masajes',         'Masaje descontracturante',       75, 480, 20],
            ['Depilación',      'Depilación piernas completas',   45, 300, 12],
            ['Manicura',        'Manicura semipermanente',        50, 220, 15],
            ['Pedicura',        'Pedicura SPA',                   60, 280, 15],
            ['Capilar',         'Tratamiento capilar nutritivo',  60, 350, 15],
            ['Maquillaje',      'Maquillaje social',              45, 250, 18],
            ['Pestañas y cejas','Lifting de pestañas',            60, 320, 18],
            ['Spa premium',     'Ritual de bienvenida (3 servicios)', 150, 990, 22],
        ];

        foreach ($tratamientos as [$cat, $n, $d, $p, $com]) {
            $catId = CategoriaTratamiento::where('nombre', $cat)->value('id');
            Tratamiento::firstOrCreate(
                ['nombre' => $n],
                [
                    'categoria_id'        => $catId,
                    'descripcion'         => $n,
                    'duracion_min'        => $d,
                    'precio'              => $p,
                    'comision_porcentaje' => $com,
                    'requiere_cabina'     => true,
                    'activo'              => true,
                ]
            );
        }
    }

    /* ========================================================
     | PROVEEDORES + CATEGORIAS + PRODUCTOS
     |========================================================*/
    protected function seedProveedores(): void
    {
        $provs = [
            ['Distribuidora Bella',    'Carla Méndez',  '+502 2222-3333', 'ventas@bella.com'],
            ['Cosméticos del Sur',     'Luis Pérez',    '+502 2334-5566', 'info@cosmeticos.com'],
            ['Insumos Spa',            'Ana Castro',    '+502 2445-6677', 'ventas@insumospa.com'],
            ['Productos Naturales GT', 'Mario Solis',   '+502 2556-7788', 'natural@gt.com'],
            ['Beauty World',           'Paola Núñez',   '+502 2667-8899', 'pn@beauty.com'],
            ['Imex Aroma',             'Roberto Díaz',  '+502 2778-9900', 'imex@aroma.com'],
            ['Dermavida',              'Lorena Ortiz',  '+502 2889-0011', 'lo@dermavida.com'],
            ['Esthetic Pro',           'Diego Morales', '+502 2990-1122', 'dm@esthetic.com'],
            ['Nail World',             'Sara Linares',  '+502 2101-2233', 'sl@nail.com'],
            ['Aromaterapias del Lago', 'Pedro Vega',    '+502 2212-3344', 'pv@aromas.com'],
        ];
        foreach ($provs as [$n, $c, $t, $e]) {
            Proveedor::firstOrCreate(['nombre' => $n], [
                'contacto' => $c, 'telefono' => $t, 'email' => $e, 'activo' => true,
            ]);
        }
    }

    protected function seedCategoriasProductos(): void
    {
        foreach (['Cremas', 'Serums', 'Mascarillas', 'Aceites', 'Shampoo', 'Esmaltes', 'Maquillaje', 'Aromaterapia', 'Accesorios', 'Otros'] as $n) {
            CategoriaProducto::firstOrCreate(['nombre' => $n], ['activo' => true]);
        }
    }

    protected function seedProductos(): void
    {
        $productos = [
            ['Cremas',       'Crema antiarrugas 50ml',        'PRD-001', 180, 320, 25, 5],
            ['Serums',       'Serum vitamina C 30ml',         'PRD-002', 220, 420, 18, 4],
            ['Mascarillas',  'Mascarilla de oro 24k',         'PRD-003',  85, 180, 30, 6],
            ['Aceites',      'Aceite esencial lavanda',       'PRD-004',  65, 130, 22, 5],
            ['Shampoo',      'Shampoo nutritivo 500ml',       'PRD-005', 120, 240,  3, 5],
            ['Esmaltes',     'Esmalte semipermanente rosa',   'PRD-006',  45,  95, 14, 8],
            ['Maquillaje',   'Base líquida tono natural',     'PRD-007', 150, 280,  9, 4],
            ['Aromaterapia', 'Vela aromática rosa mosqueta',  'PRD-008',  55, 110, 16, 5],
            ['Accesorios',   'Brocha facial profesional',     'PRD-009',  90, 175,  7, 3],
            ['Cremas',       'Crema corporal hidratante 250ml','PRD-010',100, 195,  2, 6],
        ];

        $proveedores = Proveedor::pluck('id')->toArray();

        foreach ($productos as [$cat, $n, $cod, $compra, $venta, $stock, $minimo]) {
            $catId = CategoriaProducto::where('nombre', $cat)->value('id');
            Producto::firstOrCreate(
                ['codigo' => $cod],
                [
                    'categoria_id'    => $catId,
                    'proveedor_id'    => $proveedores[array_rand($proveedores)],
                    'nombre'          => $n,
                    'precio_compra'   => $compra,
                    'precio_venta'    => $venta,
                    'stock_actual'    => $stock,
                    'stock_minimo'    => $minimo,
                    'unidad'          => 'unidad',
                    'para_venta'      => true,
                    'activo'          => true,
                ]
            );
        }
    }

    /* ========================================================
     | CLIENTES
     |========================================================*/
    protected function seedClientes(): void
    {
        $clientes = [
            ['Ana',       'García López',     '+502 5511-2233', 'ana.garcia@gmail.com',      '1990-04-15', 'F'],
            ['Beatriz',   'Morales Cruz',     '+502 5522-3344', 'beatriz.morales@gmail.com', '1988-11-22', 'F'],
            ['Carmen',    'Pérez Aguirre',    '+502 5533-4455', 'carmen.perez@gmail.com',    '1995-07-08', 'F'],
            ['Diana',     'Ramírez Soto',     '+502 5544-5566', 'diana.ramirez@gmail.com',   '1992-02-19', 'F'],
            ['Elena',     'Torres Velázquez', '+502 5555-6677', 'elena.torres@gmail.com',    '1985-09-30', 'F'],
            ['Fátima',    'Vega Castro',      '+502 5566-7788', 'fatima.vega@gmail.com',     '1998-01-12', 'F'],
            ['Gabriela',  'Hernández Méndez', '+502 5577-8899', 'gabriela.h@gmail.com',      '1991-06-04', 'F'],
            ['Helena',    'Núñez Pacheco',    '+502 5588-9900', 'helena.nunez@gmail.com',    '1987-12-18', 'F'],
            ['Isabel',    'Ortega Rivera',    '+502 5599-0011', 'isabel.ortega@gmail.com',   '1993-08-25', 'F'],
            ['Carlos',    'Méndez Solano',    '+502 5500-1122', 'carlos.mendez@gmail.com',   '1989-05-17', 'M'],
        ];

        // Distribuir created_at a lo largo de los últimos 45 días, varios "esta semana"
        foreach ($clientes as $i => [$n, $a, $t, $e, $fn, $g]) {
            $diasAtras = match (true) {
                $i < 3   => random_int(0, 6),     // 3 clientes esta semana
                $i < 6   => random_int(7, 21),    // 3 hace 1-3 semanas
                default  => random_int(22, 45),   // 4 hace 3-6 semanas
            };
            $createdAt = Carbon::now()->subDays($diasAtras)->setTime(random_int(9, 18), random_int(0, 59));

            Cliente::firstOrCreate(
                ['email' => $e],
                [
                    'nombre'          => $n,
                    'apellido'        => $a,
                    'telefono'        => $t,
                    'fecha_nacimiento'=> $fn,
                    'genero'          => $g,
                    'ciudad'          => 'Ciudad de Guatemala',
                    'activo'          => true,
                    'acepta_marketing'=> (bool) random_int(0, 1),
                    'created_at'      => $createdAt,
                    'updated_at'      => $createdAt,
                ]
            );
        }
    }

    /* ========================================================
     | BONOS PLANTILLAS
     |========================================================*/
    protected function seedBonosPlantillas(): void
    {
        $tratamientos = Tratamiento::pluck('id', 'nombre')->toArray();

        $plantillas = [
            ['Pack Facial 5 sesiones',           'Limpieza facial profunda',       1500,  5, 180],
            ['Pack Antiedad 3 sesiones',         'Antiedad con colágeno',          1800,  3, 120],
            ['Pack Masajes 10 sesiones',         'Masaje relajante 60 min',        3500, 10, 365],
            ['Pack Depilación 8 sesiones',       'Depilación piernas completas',   2200,  8, 240],
            ['Pack Manicura 6 sesiones',         'Manicura semipermanente',        1200,  6, 180],
            ['Pack Pedicura 5 sesiones',         'Pedicura SPA',                   1300,  5, 180],
            ['Pack Capilar 4 sesiones',          'Tratamiento capilar nutritivo',  1300,  4, 150],
            ['Pack Pestañas 3 sesiones',         'Lifting de pestañas',             900,  3, 120],
            ['Pack VIP completo (10 servicios)', 'Ritual de bienvenida (3 servicios)', 8800, 10, 365],
            ['Pack Corporales 6 sesiones',       'Envoltura de algas',             3000,  6, 240],
        ];

        foreach ($plantillas as [$n, $tratNombre, $p, $s, $v]) {
            BonoPlantilla::firstOrCreate(['nombre' => $n], [
                'descripcion'    => $n,
                'precio'         => $p,
                'sesiones_total' => $s,
                'validez_dias'   => $v,
                'tratamiento_id' => $tratamientos[$tratNombre] ?? null,
                'activo'         => true,
            ]);
        }
    }

    /* ========================================================
     | CITAS - distribuidas en el tiempo
     |========================================================*/
    protected function seedCitas(): void
    {
        $clientes      = Cliente::pluck('id')->toArray();
        $profesionales = User::where('rol', 'profesional')->pluck('id')->toArray();
        $cabinas       = Cabina::pluck('id')->toArray();
        $tratamientos  = Tratamiento::all();
        $admin         = User::where('rol', 'admin')->value('id');

        if (empty($clientes) || empty($profesionales) || empty($cabinas) || $tratamientos->isEmpty()) {
            return;
        }

        // 30 citas: 18 pasadas (realizadas), 6 hoy/mañana, 6 próximas semanas
        $bloques = [
            // [días offset, estado]
            [-28, 'realizada'], [-25, 'realizada'], [-22, 'realizada'],
            [-20, 'realizada'], [-18, 'realizada'], [-15, 'realizada'],
            [-13, 'realizada'], [-11, 'realizada'], [-9,  'realizada'],
            [-7,  'realizada'], [-6,  'realizada'], [-5,  'realizada'],
            [-4,  'realizada'], [-3,  'realizada'], [-2,  'realizada'],
            [-1,  'realizada'], [-1,  'no_show'],   [-2,  'cancelada'],
            [0,   'confirmada'],[0,   'confirmada'],[0,  'pendiente'],
            [1,   'confirmada'],[2,   'confirmada'],[3,  'pendiente'],
            [5,   'pendiente'], [7,   'pendiente'], [10, 'pendiente'],
            [12,  'pendiente'], [14,  'pendiente'], [21, 'pendiente'],
        ];

        $horarios = ['09:00', '10:00', '11:00', '12:00', '14:00', '15:00', '16:00', '17:00', '18:00'];

        foreach ($bloques as $idx => [$offset, $estado]) {
            $fecha = Carbon::today()->addDays($offset);
            $hora = $horarios[array_rand($horarios)];
            $tratamiento = $tratamientos->random();

            $horaInicio = Carbon::parse($hora);
            $horaFin = $horaInicio->copy()->addMinutes($tratamiento->duracion_min);

            $cita = Cita::create([
                'cliente_id'     => $clientes[array_rand($clientes)],
                'profesional_id' => $profesionales[array_rand($profesionales)],
                'cabina_id'      => $cabinas[array_rand($cabinas)],
                'fecha'          => $fecha->format('Y-m-d'),
                'hora_inicio'    => $horaInicio->format('H:i:s'),
                'hora_fin'       => $horaFin->format('H:i:s'),
                'estado'         => $estado,
                'total'          => $tratamiento->precio,
                'creado_por'     => $admin,
                'created_at'     => $fecha->copy()->setTime(8, 0),
                'updated_at'     => $fecha->copy()->setTime(8, 0),
            ]);

            CitaServicio::create([
                'cita_id'        => $cita->id,
                'tratamiento_id' => $tratamiento->id,
                'descripcion'    => $tratamiento->nombre,
                'duracion_min'   => $tratamiento->duracion_min,
                'precio'         => $tratamiento->precio,
            ]);
        }
    }

    /* ========================================================
     | BONOS VENDIDOS
     |========================================================*/
    protected function seedBonos(): void
    {
        $clientes   = Cliente::pluck('id')->toArray();
        $plantillas = BonoPlantilla::all();
        if (empty($clientes) || $plantillas->isEmpty()) return;

        for ($i = 0; $i < 10; $i++) {
            $plantilla = $plantillas->random();
            $diasAtras = random_int(1, 50);
            $fechaCompra = Carbon::today()->subDays($diasAtras);
            $usadas = random_int(0, max(1, intval($plantilla->sesiones_total / 2)));

            Bono::create([
                'codigo'             => 'BNO-' . strtoupper(Str::random(6)),
                'cliente_id'         => $clientes[array_rand($clientes)],
                'plantilla_id'       => $plantilla->id,
                'nombre'             => $plantilla->nombre,
                'sesiones_total'     => $plantilla->sesiones_total,
                'sesiones_usadas'    => $usadas,
                'fecha_compra'       => $fechaCompra->format('Y-m-d'),
                'fecha_vencimiento'  => $fechaCompra->copy()->addDays($plantilla->validez_dias)->format('Y-m-d'),
                'precio_pagado'      => $plantilla->precio,
                'estado'             => $usadas >= $plantilla->sesiones_total ? 'agotado' : 'activo',
                'created_at'         => $fechaCompra,
                'updated_at'         => $fechaCompra,
            ]);
        }
    }

    /* ========================================================
     | VENTAS - distribuidas últimos 60 días
     |========================================================*/
    protected function seedVentas(): void
    {
        $clientes   = Cliente::pluck('id')->toArray();
        $usuarios   = User::pluck('id')->toArray();
        $profes     = User::where('rol', 'profesional')->pluck('id')->toArray();
        $tratamientos = Tratamiento::all();
        $productos    = Producto::all();
        if (empty($clientes) || empty($usuarios) || $tratamientos->isEmpty()) return;

        $metodos = ['efectivo', 'tarjeta', 'transferencia', 'efectivo', 'tarjeta'];
        $consec = 1;

        // Generar 35 ventas distribuidas en los últimos 60 días con buena densidad reciente
        $ventasACrear = [
            // hoy
            0, 0, 0,
            // ayer y antier
            -1, -1, -2, -2,
            // últimos 7 días
            -3, -4, -5, -5, -6, -7,
            // últimas 2 semanas
            -8, -10, -11, -13, -14,
            // últimas 4 semanas
            -16, -18, -20, -22, -24, -26, -28,
            // últimas 8 semanas
            -32, -35, -40, -44, -48, -52, -55, -58,
        ];

        foreach ($ventasACrear as $offset) {
            $fecha = Carbon::today()->addDays($offset)
                ->setTime(random_int(10, 19), random_int(0, 59));

            // 1-3 items por venta
            $numItems = random_int(1, 3);
            $items = [];
            $subtotal = 0;

            for ($i = 0; $i < $numItems; $i++) {
                if (random_int(0, 100) < 65 && $tratamientos->isNotEmpty()) {
                    // servicio
                    $t = $tratamientos->random();
                    $cant = 1;
                    $precio = (float) $t->precio;
                    $items[] = [
                        'tipo'             => 'servicio',
                        'referencia_id'    => $t->id,
                        'profesional_id'   => $profes ? $profes[array_rand($profes)] : null,
                        'descripcion'      => $t->nombre,
                        'cantidad'         => $cant,
                        'precio_unitario'  => $precio,
                        'descuento'        => 0,
                        'subtotal'         => $cant * $precio,
                    ];
                    $subtotal += $cant * $precio;
                } elseif ($productos->isNotEmpty()) {
                    // producto
                    $p = $productos->random();
                    $cant = random_int(1, 2);
                    $precio = (float) $p->precio_venta;
                    $items[] = [
                        'tipo'             => 'producto',
                        'referencia_id'    => $p->id,
                        'profesional_id'   => null,
                        'descripcion'      => $p->nombre,
                        'cantidad'         => $cant,
                        'precio_unitario'  => $precio,
                        'descuento'        => 0,
                        'subtotal'         => $cant * $precio,
                    ];
                    $subtotal += $cant * $precio;
                }
            }

            $impuestoIncluido = true;
            $impuesto = $impuestoIncluido ? 0 : round($subtotal * 0.12, 2);
            $total = $subtotal + $impuesto;

            $venta = Venta::create([
                'numero'      => 'V-' . $fecha->format('Ym') . '-' . str_pad((string) $consec, 4, '0', STR_PAD_LEFT),
                'cliente_id'  => $clientes[array_rand($clientes)],
                'user_id'     => $usuarios[array_rand($usuarios)],
                'fecha'       => $fecha,
                'subtotal'    => $subtotal,
                'descuento'   => 0,
                'impuesto'    => $impuesto,
                'total'       => $total,
                'metodo_pago' => $metodos[array_rand($metodos)],
                'estado'      => 'pagada',
                'created_at'  => $fecha,
                'updated_at'  => $fecha,
            ]);
            $consec++;

            foreach ($items as $itm) {
                $itm['venta_id'] = $venta->id;
                VentaItem::create($itm);
            }

            VentaPago::create([
                'venta_id'  => $venta->id,
                'metodo'    => $venta->metodo_pago,
                'monto'     => $total,
            ]);
        }
    }

    /* ========================================================
     | MOVIMIENTOS DE STOCK
     |========================================================*/
    protected function seedMovimientosStock(): void
    {
        $productos = Producto::all();
        $userId = User::where('rol', 'admin')->value('id');
        if ($productos->isEmpty()) return;

        foreach ($productos as $p) {
            // 1 entrada inicial hace 45-60 días
            $fechaEntrada = Carbon::today()->subDays(random_int(45, 60));
            MovimientoStock::create([
                'producto_id'    => $p->id,
                'tipo'           => 'entrada',
                'cantidad'       => $p->stock_actual + random_int(5, 15),
                'stock_anterior' => 0,
                'stock_nuevo'    => $p->stock_actual + random_int(5, 15),
                'motivo'         => 'Compra inicial',
                'user_id'        => $userId,
                'created_at'     => $fechaEntrada,
                'updated_at'     => $fechaEntrada,
            ]);
        }
    }
}
