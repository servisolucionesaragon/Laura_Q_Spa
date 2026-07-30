<?php

namespace App\Traits;

trait TieneWhatsapp
{
    /**
     * Limpia el teléfono guardado y le antepone el indicativo de Colombia (57)
     * si no lo trae ya. No valida que el número exista de verdad, solo lo
     * normaliza al formato que espera wa.me.
     */
    public function numeroWhatsapp(): ?string
    {
        $tel = $this->telefono ?? null;
        if (! $tel) {
            return null;
        }

        $limpio = preg_replace('/\D+/', '', $tel);
        if (! $limpio) {
            return null;
        }

        if (! str_starts_with($limpio, '57') || strlen($limpio) <= 10) {
            $limpio = '57' . ltrim($limpio, '0');
        }

        return $limpio;
    }

    public function whatsappUrl(string $mensaje = ''): ?string
    {
        $numero = $this->numeroWhatsapp();
        if (! $numero) {
            return null;
        }

        $url = 'https://wa.me/' . $numero;
        if ($mensaje !== '') {
            $url .= '?text=' . rawurlencode($mensaje);
        }

        return $url;
    }
}
