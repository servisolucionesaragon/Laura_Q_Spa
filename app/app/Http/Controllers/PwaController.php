<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionEmpresa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PwaController extends Controller
{
    public function manifest(): JsonResponse
    {
        $config = ConfiguracionEmpresa::first();
        $nombre = $config->nombre_empresa ?? config('app.name');
        $color = $config->color_primario ?? '#ff78a2';

        return response()->json([
            'name' => $nombre,
            'short_name' => $nombre,
            'start_url' => url('/'),
            'scope' => url('/'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => $color,
            'icons' => [
                [
                    'src' => route('pwa.icon', ['size' => 192]),
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
                [
                    'src' => route('pwa.icon', ['size' => 512]),
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable',
                ],
            ],
        ])->header('Content-Type', 'application/manifest+json');
    }

    public function icon(string $size): Response
    {
        $size = in_array($size, ['192', '512'], true) ? (int) $size : 192;
        $config = ConfiguracionEmpresa::first();

        $timestamp = $config?->updated_at?->timestamp ?? 0;
        $cachePath = "pwa/icon-{$size}-{$timestamp}.png";

        if (! Storage::disk('public')->exists($cachePath)) {
            Storage::disk('public')->put($cachePath, $this->generarIcono($config, $size));
        }

        return response(Storage::disk('public')->get($cachePath), 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000, immutable');
    }

    private function generarIcono(?ConfiguracionEmpresa $config, int $size): string
    {
        $lienzo = imagecreatetruecolor($size, $size);
        imagesavealpha($lienzo, true);
        $transparente = imagecolorallocatealpha($lienzo, 0, 0, 0, 127);
        imagefill($lienzo, 0, 0, $transparente);

        $logoPath = $config?->logo ? Storage::disk('public')->path($config->logo) : null;
        $origen = null;

        if ($logoPath && file_exists($logoPath)) {
            $info = @getimagesize($logoPath);
            $origen = match ($info[2] ?? null) {
                IMAGETYPE_JPEG => imagecreatefromjpeg($logoPath),
                IMAGETYPE_PNG => imagecreatefrompng($logoPath),
                IMAGETYPE_GIF => imagecreatefromgif($logoPath),
                IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($logoPath) : null,
                IMAGETYPE_BMP => function_exists('imagecreatefrombmp') ? imagecreatefrombmp($logoPath) : null,
                default => null,
            };
        }

        if ($origen) {
            imagesavealpha($origen, true);
            $anchoOrig = imagesx($origen);
            $altoOrig = imagesy($origen);
            $escala = min($size / $anchoOrig, $size / $altoOrig);
            $anchoNuevo = (int) round($anchoOrig * $escala);
            $altoNuevo = (int) round($altoOrig * $escala);
            $x = (int) (($size - $anchoNuevo) / 2);
            $y = (int) (($size - $altoNuevo) / 2);

            imagecopyresampled($lienzo, $origen, $x, $y, 0, 0, $anchoNuevo, $altoNuevo, $anchoOrig, $altoOrig);
            imagedestroy($origen);
        } else {
            $hex = ltrim($config->color_primario ?? '#ff78a2', '#');
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            [$r, $g, $b] = array_map(fn ($c) => hexdec($c), str_split($hex, 2));
            $color = imagecolorallocate($lienzo, $r, $g, $b);
            imagefilledrectangle($lienzo, 0, 0, $size, $size, $color);
        }

        ob_start();
        imagepng($lienzo);
        $contenido = ob_get_clean();
        imagedestroy($lienzo);

        return $contenido;
    }
}
