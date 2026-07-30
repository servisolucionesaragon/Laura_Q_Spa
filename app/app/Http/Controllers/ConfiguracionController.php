<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionEmpresa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConfiguracionController extends Controller
{
    public function edit(): View
    {
        $configuracion = ConfiguracionEmpresa::obtener();
        return view('configuracion.edit', compact('configuracion'));
    }

    public function update(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'nombre_empresa'      => ['required', 'string', 'max:191'],
            'razon_social'        => ['nullable', 'string', 'max:191'],
            'nit_rfc'             => ['nullable', 'string', 'max:50'],
            'telefono'            => ['nullable', 'string', 'max:30'],
            'email'               => ['nullable', 'email', 'max:191'],
            'sitio_web'           => ['nullable', 'string', 'max:191'],
            'direccion'           => ['nullable', 'string', 'max:191'],
            'ciudad'              => ['nullable', 'string', 'max:100'],
            'pais'                => ['nullable', 'string', 'max:100'],
            'simbolo_moneda'      => ['required', 'string', 'max:5'],
            'codigo_moneda'       => ['required', 'string', 'max:5'],
            'formato_moneda'      => ['required', 'in:symbol_amount,amount_symbol'],
            'impuesto_porcentaje' => ['required', 'numeric', 'min:0', 'max:100'],
            'nombre_impuesto'     => ['required', 'string', 'max:30'],
            'impuesto_incluido'   => ['nullable', 'boolean'],
            'zona_horaria'        => ['required', 'string', 'max:50'],
            'color_primario'      => ['required', 'string', 'max:10'],
            'color_secundario'    => ['required', 'string', 'max:10'],
            'color_accent'        => ['required', 'string', 'max:10'],
            'color_sidebar_fondo' => ['required', 'string', 'max:10'],
            'color_sidebar_texto' => ['required', 'string', 'max:10'],
            'hora_apertura'       => ['required', 'date_format:H:i'],
            'hora_cierre'         => ['required', 'date_format:H:i'],
            'dias_laborales'      => ['nullable', 'array'],
            'dias_laborales.*'    => ['in:lun,mar,mie,jue,vie,sab,dom'],
            'intervalo_citas_min' => ['required', 'integer', 'min:5', 'max:240'],
            'mensaje_recibo'      => ['nullable', 'string', 'max:500'],
            'terminos_condiciones' => ['nullable', 'string'],
            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,svg,webp', 'max:2048'],
            'eliminar_logo'       => ['nullable', 'boolean'],
        ]);

        $configuracion = ConfiguracionEmpresa::obtener();

        $datos['impuesto_incluido'] = $request->boolean('impuesto_incluido');

        if ($request->boolean('eliminar_logo') && $configuracion->logo) {
            Storage::disk('public')->delete($configuracion->logo);
            $datos['logo'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($configuracion->logo) {
                Storage::disk('public')->delete($configuracion->logo);
            }
            $datos['logo'] = $request->file('logo')->store('logos', 'public');
        } else {
            unset($datos['logo']);
        }
        unset($datos['eliminar_logo']);

        $configuracion->update($datos);

        return redirect()
            ->route('configuracion.edit')
            ->with('success', 'Configuración actualizada correctamente.');
    }
}
