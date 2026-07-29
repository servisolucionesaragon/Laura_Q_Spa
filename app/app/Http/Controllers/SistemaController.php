<?php

namespace App\Http\Controllers;

use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SistemaController extends Controller
{
    public function __construct(protected BackupService $backups) {}

    /**
     * Pantalla principal del módulo de copia de seguridad.
     */
    public function backup()
    {
        $items = $this->backups->listar();
        return view('sistema.backup', [
            'backups' => $items,
            'totalBackups' => count($items),
            'tamanoTotal'  => array_sum(array_column($items, 'tamano')),
        ]);
    }

    /**
     * Crear una nueva copia de seguridad.
     */
    public function crearBackup(): RedirectResponse
    {
        try {
            $info = $this->backups->crear();
            return redirect()
                ->route('sistema.backup')
                ->with('success', "Copia generada correctamente: {$info['nombre']} ({$info['tamano_h']})");
        } catch (\Throwable $e) {
            return redirect()
                ->route('sistema.backup')
                ->with('error', 'No se pudo generar la copia: ' . $e->getMessage());
        }
    }

    /**
     * Descargar una copia.
     */
    public function descargarBackup(string $nombre): BinaryFileResponse|RedirectResponse
    {
        $ruta = $this->backups->obtenerRuta($nombre);
        if (! $ruta) {
            return redirect()->route('sistema.backup')->with('error', 'La copia solicitada no existe.');
        }
        return response()->download($ruta, $nombre, [
            'Content-Type' => 'application/zip',
        ]);
    }

    /**
     * Eliminar una copia.
     */
    public function eliminarBackup(Request $request): RedirectResponse
    {
        $request->validate(['nombre' => 'required|string']);
        $ok = $this->backups->eliminar($request->input('nombre'));
        return redirect()->route('sistema.backup')->with(
            $ok ? 'success' : 'error',
            $ok ? 'Copia eliminada correctamente.' : 'No fue posible eliminar la copia.'
        );
    }

    /**
     * Restaurar el sistema desde una copia.
     * Permite subir un .zip nuevo o usar una copia existente.
     */
    public function restaurarBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'archivo'         => 'nullable|file|max:51200', // 50MB
            'backup_existente'=> 'nullable|string',
            'confirmacion'    => 'required|in:RESTAURAR',
        ], [
            'confirmacion.required' => 'Debes escribir RESTAURAR para confirmar la acción.',
            'confirmacion.in'       => 'La palabra de confirmación no coincide.',
        ]);

        $rutaZip = null;

        if ($request->hasFile('archivo')) {
            $rutaZip = $request->file('archivo')->getRealPath();
        } elseif ($request->filled('backup_existente')) {
            $rutaZip = $this->backups->obtenerRuta($request->input('backup_existente'));
        }

        if (! $rutaZip) {
            return redirect()->route('sistema.backup')
                ->with('error', 'Debes seleccionar una copia existente o subir un archivo de respaldo.');
        }

        try {
            $this->backups->restaurar($rutaZip);

            // Cerrar sesión actual: el usuario puede no existir tras la restauración
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Sistema restaurado correctamente. Por favor inicia sesión nuevamente.');
        } catch (\Throwable $e) {
            return redirect()->route('sistema.backup')
                ->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    /**
     * Resetear el sistema para empezar de cero (empresa nueva).
     */
    public function resetSistema(Request $request): RedirectResponse
    {
        $request->validate([
            'tipo'         => 'required|in:soft,hard',
            'confirmacion' => 'required',
            'crear_backup_previo' => 'nullable|boolean',
        ]);

        $palabraEsperada = $request->input('tipo') === 'hard' ? 'BORRAR TODO' : 'RESETEAR';
        if ($request->input('confirmacion') !== $palabraEsperada) {
            return redirect()->route('sistema.backup')
                ->with('error', "Para confirmar debes escribir exactamente: {$palabraEsperada}");
        }

        try {
            // Crear backup previo si se solicita
            $infoBackup = null;
            if ($request->boolean('crear_backup_previo')) {
                $infoBackup = $this->backups->crear();
            }

            $resumen = $this->backups->reset(
                $request->input('tipo'),
                $request->input('tipo') === 'hard' ? Auth::id() : null
            );

            $tablasOp = array_sum($resumen['operacional']);
            $tablasCat = array_sum($resumen['catalogos'] ?? []);
            $msg = $request->input('tipo') === 'hard'
                ? "Sistema reseteado completamente. Se eliminaron {$tablasOp} registros operacionales, {$tablasCat} de catálogos, {$resumen['usuarios']} usuarios y la configuración de la empresa."
                : "Datos operacionales eliminados ({$tablasOp} registros). Los catálogos, empleados y configuración se mantienen intactos.";

            if ($infoBackup) {
                $msg .= " Se generó una copia previa: {$infoBackup['nombre']}.";
            }

            // Si hard reset, redirigir a configurar
            if ($request->input('tipo') === 'hard') {
                return redirect()->route('configuracion.edit')->with('success', $msg);
            }

            return redirect()->route('dashboard')->with('success', $msg);
        } catch (\Throwable $e) {
            return redirect()->route('sistema.backup')
                ->with('error', 'Error al resetear el sistema: ' . $e->getMessage());
        }
    }
}
