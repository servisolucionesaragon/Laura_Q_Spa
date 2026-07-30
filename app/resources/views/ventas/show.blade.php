@extends($layout ?? 'layouts.app')
@section('titulo', 'Ticket ' . $venta->numero)

@push('styles')
<style>
@media print {
    .spa-sidebar, .spa-topbar, .no-print { display: none !important; }
    .spa-main { margin: 0 !important; }
    .spa-content { padding: 0 !important; }
}
.ticket {
    max-width: 480px; margin: 0 auto;
    background: #fff;
    padding: 2rem;
    border-radius: var(--spa-radius);
    box-shadow: var(--spa-shadow);
    border: 1px solid var(--spa-border);
}
.ticket h2 { color: var(--spa-secondary); text-align: center; margin: 0 0 .25rem; }
.ticket .empresa { text-align: center; color: var(--spa-muted); font-size: .85rem; margin-bottom: 1.2rem; }
.ticket .num { text-align: center; font-family: 'Courier New',monospace; font-size: 1.2rem; letter-spacing: 2px; padding: .5rem; background: var(--spa-bg-light); border-radius: 8px; margin-bottom: 1rem; color: var(--spa-secondary); font-weight: 700; }
.ticket .info { font-size: .85rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px dashed var(--spa-border); }
.ticket .info div { display: flex; justify-content: space-between; padding: 2px 0; }
.ticket-items { width: 100%; font-size: .85rem; margin-bottom: 1rem; }
.ticket-items td { padding: 4px 2px; vertical-align: top; }
.ticket-items thead td { border-bottom: 1px solid var(--spa-border); padding-bottom: 6px; font-weight: 700; color: var(--spa-secondary); text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; }
.ticket-tot { border-top: 2px dashed var(--spa-border); padding-top: .75rem; font-size: .9rem; }
.ticket-tot div { display: flex; justify-content: space-between; padding: 2px 0; }
.ticket-tot div.gran { font-size: 1.2rem; font-weight: 800; color: var(--spa-secondary); padding-top: .5rem; border-top: 1px solid var(--spa-border); margin-top: .35rem; }
.ticket-msg { text-align: center; color: var(--spa-muted); font-size: .85rem; font-style: italic; margin-top: 1.5rem; }
.ticket-logo { display: block; max-height: 70px; max-width: 200px; margin: 0 auto .75rem; object-fit: contain; }
</style>
@endpush

@section('contenido')
@php $sim = $configEmpresa?->simbolo_moneda ?? 'Q'; @endphp

<div class="d-flex justify-content-between mb-3 no-print flex-wrap gap-2">
    @if(! ($publico ?? false))
        <a href="{{ route('ventas.index') }}" class="btn btn-spa-secondary"><i class="bi bi-arrow-left"></i> Volver</a>
    @else
        <span></span>
    @endif
    <div class="d-flex gap-2">
        @if(! ($publico ?? false) && $venta->cliente?->numeroWhatsapp())
            <a href="{{ $venta->cliente->whatsappUrl('Hola ' . $venta->cliente->nombre . ', te compartimos tu comprobante de compra en *' . ($configEmpresa->nombre_empresa ?? 'nuestro spa') . '*: ' . \Illuminate\Support\Facades\URL::signedRoute('publico.venta.recibo', $venta)) }}"
               target="_blank" class="btn" style="background:#25D366;color:#fff">
                <i class="bi bi-whatsapp"></i> Enviar por WhatsApp
            </a>
        @endif
        <button onclick="window.print()" class="btn btn-spa-primary"><i class="bi bi-printer"></i> Imprimir ticket</button>
        @if(! ($publico ?? false) && $venta->estado === 'pagada')
        <form action="{{ route('ventas.anular', $venta) }}" method="POST" onsubmit="return confirm('¿Anular esta venta?')">
            @csrf
            <button class="btn" style="background:var(--spa-danger);color:#fff"><i class="bi bi-x-circle"></i> Anular</button>
        </form>
        @endif
    </div>
</div>

<div class="ticket">
    @if($configEmpresa?->logoUrl())
        <img src="{{ $configEmpresa->logoUrl() }}" alt="Logo" class="ticket-logo">
    @endif
    <h2>{{ $configEmpresa?->nombre_empresa ?? 'Estética & SPA' }}</h2>
    <div class="empresa">
        @if($configEmpresa?->direccion){{ $configEmpresa->direccion }}<br>@endif
        @if($configEmpresa?->telefono){{ $configEmpresa->telefono }}@endif
        @if($configEmpresa?->nit_rfc) · NIT/RFC: {{ $configEmpresa->nit_rfc }}@endif
    </div>

    <div class="num">{{ $venta->numero }}</div>

    <div class="info">
        <div><span>Fecha</span><strong>{{ $venta->fecha->format('d/m/Y H:i') }}</strong></div>
        <div><span>Cliente</span><strong>{{ $venta->cliente?->nombre_completo ?? 'Cliente eventual' }}</strong></div>
        <div><span>Cajero</span><strong>{{ $venta->user?->name ?? '—' }}</strong></div>
        <div><span>Método</span><strong>{{ ucfirst($venta->metodo_pago) }}</strong></div>
        @if($venta->estado === 'anulada')<div style="color:var(--spa-danger);text-align:center;font-weight:700;margin-top:.5rem">★ ANULADA ★</div>@endif
    </div>

    <table class="ticket-items">
        <thead><tr><td>Concepto</td><td style="text-align:center">Cant</td><td style="text-align:right">Subtotal</td></tr></thead>
        <tbody>
        @foreach($venta->items as $it)
            <tr>
                <td>{{ $it->descripcion }}<br><small style="color:var(--spa-muted)">{{ $sim }} {{ number_format($it->precio_unitario, 2) }} c/u</small></td>
                <td style="text-align:center">{{ rtrim(rtrim($it->cantidad, '0'), '.') }}</td>
                <td style="text-align:right"><strong>{{ $sim }} {{ number_format($it->subtotal, 2) }}</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="ticket-tot">
        <div><span>Subtotal</span><span>{{ $sim }} {{ number_format($venta->subtotal, 2) }}</span></div>
        @if($venta->descuento > 0)<div><span>Descuento</span><span>− {{ $sim }} {{ number_format($venta->descuento, 2) }}</span></div>@endif
        @if($venta->impuesto > 0)<div><span>{{ $configEmpresa?->nombre_impuesto ?? 'IVA' }} ({{ $configEmpresa?->impuesto_porcentaje ?? 0 }}%)</span><span>{{ $sim }} {{ number_format($venta->impuesto, 2) }}</span></div>@endif
        <div class="gran"><span>TOTAL</span><span>{{ $sim }} {{ number_format($venta->total, 2) }}</span></div>
    </div>

    @if($configEmpresa?->mensaje_recibo)
        <div class="ticket-msg">{{ $configEmpresa->mensaje_recibo }}</div>
    @endif
</div>
@endsection
