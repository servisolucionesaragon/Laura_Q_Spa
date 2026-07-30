<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use ZipArchive;

class BackupService
{
    protected string $directorio;

    public function __construct()
    {
        $this->directorio = storage_path('app/backups');
        if (! is_dir($this->directorio)) {
            @mkdir($this->directorio, 0755, true);
        }
    }

    /* =====================================================
     | LISTAR
     |======================================================*/
    public function listar(): array
    {
        if (! is_dir($this->directorio)) return [];

        $archivos = glob($this->directorio . DIRECTORY_SEPARATOR . 'backup-*.zip') ?: [];
        $items = [];
        foreach ($archivos as $ruta) {
            $items[] = [
                'nombre'    => basename($ruta),
                'ruta'      => $ruta,
                'tamano'    => filesize($ruta),
                'tamano_h'  => $this->formatearTamano(filesize($ruta)),
                'fecha'     => date('Y-m-d H:i:s', filemtime($ruta)),
                'fecha_iso' => date('c', filemtime($ruta)),
            ];
        }
        usort($items, fn ($a, $b) => strcmp($b['fecha'], $a['fecha']));
        return $items;
    }

    public function obtenerRuta(string $nombre): ?string
    {
        $nombre = basename($nombre); // sanitizar
        $ruta   = $this->directorio . DIRECTORY_SEPARATOR . $nombre;
        return file_exists($ruta) ? $ruta : null;
    }

    public function eliminar(string $nombre): bool
    {
        $ruta = $this->obtenerRuta($nombre);
        return $ruta ? @unlink($ruta) : false;
    }

    /* =====================================================
     | CREAR BACKUP
     |======================================================*/
    public function crear(): array
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('La extensión PHP "zip" no está habilitada.');
        }

        $nombre = 'backup-' . date('Y-m-d_His') . '.zip';
        $rutaZip = $this->directorio . DIRECTORY_SEPARATOR . $nombre;

        // 1) Generar SQL
        $sql = $this->generarSqlDump();

        // 2) Crear ZIP
        $zip = new ZipArchive();
        $abierto = $zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($abierto !== true) {
            throw new RuntimeException('No fue posible crear el archivo ZIP de respaldo.');
        }

        $zip->addFromString('database.sql', $sql);

        $meta = [
            'fecha'    => now()->toIso8601String(),
            'app'      => config('app.name'),
            'database' => config('database.connections.mysql.database'),
            'php'      => phpversion(),
            'version'  => '1.0',
        ];
        $zip->addFromString('backup.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 3) Adjuntar archivos públicos del storage (logos, imágenes)
        $publico = storage_path('app/public');
        if (is_dir($publico)) {
            $iter = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publico, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iter as $info) {
                if ($info->isFile()) {
                    $rel = ltrim(str_replace($publico, '', $info->getPathname()), DIRECTORY_SEPARATOR);
                    $zip->addFile($info->getPathname(), 'storage/' . str_replace('\\', '/', $rel));
                }
            }
        }

        $zip->close();

        return [
            'nombre'   => $nombre,
            'ruta'     => $rutaZip,
            'tamano'   => filesize($rutaZip),
            'tamano_h' => $this->formatearTamano(filesize($rutaZip)),
        ];
    }

    /**
     * Genera un dump SQL portable (CREATE TABLE + INSERT) usando solo PDO.
     */
    protected function generarSqlDump(): string
    {
        $base = config('database.connections.mysql.database');

        $sql  = "-- =============================================\n";
        $sql .= "-- TPV Estética y SPA · Backup\n";
        $sql .= "-- Base de datos: {$base}\n";
        $sql .= "-- Generado: " . date('Y-m-d H:i:s') . "\n";
        $sql .= "-- =============================================\n\n";

        $sql .= "SET NAMES utf8mb4;\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        $tablas = DB::select('SHOW TABLES');
        $col = 'Tables_in_' . $base;

        foreach ($tablas as $row) {
            $tabla = $row->{$col};

            // CREATE TABLE
            $crear = DB::select("SHOW CREATE TABLE `{$tabla}`");
            $createStmt = $crear[0]->{'Create Table'} ?? null;
            if (! $createStmt) continue;

            $sql .= "-- ----------------------------------------\n";
            $sql .= "-- Tabla: {$tabla}\n";
            $sql .= "-- ----------------------------------------\n";
            $sql .= "DROP TABLE IF EXISTS `{$tabla}`;\n";
            $sql .= $createStmt . ";\n\n";

            // INSERTs
            $datos = DB::select("SELECT * FROM `{$tabla}`");
            if (! empty($datos)) {
                foreach ($datos as $registro) {
                    $columnas = array_keys((array) $registro);
                    $valores  = array_map(function ($v) {
                        if (is_null($v)) return 'NULL';
                        if (is_bool($v)) return $v ? '1' : '0';
                        if (is_numeric($v) && ! str_starts_with((string) $v, '0') || $v === '0' || $v === 0) {
                            // Si es numérico, dejarlo sin comillas (pero cuidado con leading zeros)
                            return is_int($v) || is_float($v) ? (string) $v : "'" . $this->escapar((string) $v) . "'";
                        }
                        return "'" . $this->escapar((string) $v) . "'";
                    }, array_values((array) $registro));

                    $sql .= "INSERT INTO `{$tabla}` (`" . implode('`,`', $columnas) . "`) VALUES ("
                          . implode(',', $valores) . ");\n";
                }
                $sql .= "\n";
            }
        }

        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        return $sql;
    }

    protected function escapar(string $valor): string
    {
        return str_replace(
            ["\\",   "\0",  "\n",  "\r",  "'",   '"',   "\x1a"],
            ["\\\\", "\\0", "\\n", "\\r", "\\'", '\\"', "\\Z"],
            $valor
        );
    }

    /* =====================================================
     | RESTAURAR BACKUP
     |======================================================*/
    public function restaurar(string $rutaZip): void
    {
        if (! file_exists($rutaZip)) {
            throw new RuntimeException('El archivo de respaldo no existe.');
        }
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException('La extensión PHP "zip" no está habilitada.');
        }

        $temp = $this->directorio . DIRECTORY_SEPARATOR . 'restore-' . time();
        if (! @mkdir($temp, 0755, true) && ! is_dir($temp)) {
            throw new RuntimeException('No se pudo crear el directorio temporal.');
        }

        $zip = new ZipArchive();
        if ($zip->open($rutaZip) !== true) {
            $this->borrarDir($temp);
            throw new RuntimeException('El archivo no es un ZIP válido.');
        }
        $zip->extractTo($temp);
        $zip->close();

        $rutaSql = $temp . DIRECTORY_SEPARATOR . 'database.sql';
        if (! file_exists($rutaSql)) {
            $this->borrarDir($temp);
            throw new RuntimeException('El archivo no contiene un dump de base de datos válido (database.sql).');
        }

        try {
            $sql = file_get_contents($rutaSql);
            $statements = $this->dividirSql($sql);

            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            foreach ($statements as $stmt) {
                $stmt = trim($stmt);
                if ($stmt === '' || str_starts_with($stmt, '--')) continue;
                DB::unprepared($stmt);
            }
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        } catch (\Throwable $e) {
            $this->borrarDir($temp);
            throw new RuntimeException('Error al ejecutar el SQL de restauración: ' . $e->getMessage(), 0, $e);
        }

        // Restaurar archivos del storage
        $dirStorage = $temp . DIRECTORY_SEPARATOR . 'storage';
        if (is_dir($dirStorage)) {
            $this->copiarDir($dirStorage, storage_path('app/public'));
        }

        $this->borrarDir($temp);
    }

    protected function dividirSql(string $sql): array
    {
        // Quitar líneas de comentario y dividir por ; al final de línea
        $limpio = preg_replace('/^\s*--.*$/m', '', $sql) ?? $sql;
        $partes = preg_split('/;\s*[\r\n]+/', $limpio) ?: [];
        return array_filter($partes, fn ($s) => trim($s) !== '');
    }

    /* =====================================================
     | RESET (vaciar para empresa nueva)
     |======================================================*/
    /**
     * @param string $tipo  'soft' | 'hard'
     * @param int|null $userIdMantener  ID del admin actual (para no auto-eliminarse en hard)
     */
    public function reset(string $tipo, ?int $userIdMantener = null): array
    {
        // Operacional: siempre se borra
        $operacional = [
            'venta_pagos', 'venta_items', 'ventas',
            'bono_consumos', 'bonos',
            'cita_servicios', 'citas',
            'movimientos_stock',
            'movimientos_caja', 'cajas',
        ];

        // Catálogos: se borran solo en hard reset
        $catalogos = [
            'clientes', 'bonos_plantillas',
            'productos', 'proveedores', 'categorias_productos',
            'tratamientos', 'categorias_tratamientos',
            'cabinas', 'metodos_pago',
        ];

        $resumen = ['operacional' => [], 'catalogos' => [], 'usuarios' => 0, 'configuracion' => false];

        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($operacional as $t) {
            $count = DB::table($t)->count();
            DB::table($t)->truncate();
            $resumen['operacional'][$t] = $count;
        }

        if ($tipo === 'hard') {
            foreach ($catalogos as $t) {
                $count = DB::table($t)->count();
                DB::table($t)->truncate();
                $resumen['catalogos'][$t] = $count;
            }

            // Eliminar todos los usuarios excepto el admin actual
            $q = DB::table('users');
            if ($userIdMantener) {
                $q->where('id', '!=', $userIdMantener);
            }
            $resumen['usuarios'] = $q->count();
            $q->delete();

            // Resetear configuración a valores por defecto
            DB::table('configuracion_empresa')->delete();
            $resumen['configuracion'] = true;

            // Re-sembrar métodos de pago básicos: sin al menos uno, el TPV
            // queda inutilizable (la validación de venta exige que el método
            // elegido exista en la tabla).
            $ahora = now();
            DB::table('metodos_pago')->insert([
                ['nombre' => 'efectivo', 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora],
                ['nombre' => 'tarjeta', 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora],
                ['nombre' => 'transferencia', 'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora],
            ]);

            // Limpiar logos
            $logos = storage_path('app/public/logos');
            if (is_dir($logos)) {
                $this->vaciarDir($logos);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Limpiar caché de Laravel para que tome la nueva config
        try {
            \Illuminate\Support\Facades\Cache::flush();
        } catch (\Throwable $e) { /* noop */ }

        return $resumen;
    }

    /* =====================================================
     | UTILIDADES
     |======================================================*/
    protected function formatearTamano(int $bytes): string
    {
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }

    protected function copiarDir(string $origen, string $destino): void
    {
        if (! is_dir($destino)) @mkdir($destino, 0755, true);
        $dir = opendir($origen);
        while (($entrada = readdir($dir)) !== false) {
            if ($entrada === '.' || $entrada === '..') continue;
            $src = $origen . DIRECTORY_SEPARATOR . $entrada;
            $dst = $destino . DIRECTORY_SEPARATOR . $entrada;
            if (is_dir($src)) $this->copiarDir($src, $dst);
            else @copy($src, $dst);
        }
        closedir($dir);
    }

    protected function borrarDir(string $dir): void
    {
        if (! is_dir($dir)) return;
        $entries = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($entries as $entrada) {
            $ruta = $dir . DIRECTORY_SEPARATOR . $entrada;
            is_dir($ruta) ? $this->borrarDir($ruta) : @unlink($ruta);
        }
        @rmdir($dir);
    }

    protected function vaciarDir(string $dir): void
    {
        if (! is_dir($dir)) return;
        $entries = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($entries as $entrada) {
            $ruta = $dir . DIRECTORY_SEPARATOR . $entrada;
            is_dir($ruta) ? $this->borrarDir($ruta) : @unlink($ruta);
        }
    }
}
