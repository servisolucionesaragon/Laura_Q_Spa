@php
    $idInput = $idInput ?? 'clienteBuscador';
    $nombreCampo = $nombreCampo ?? 'cliente_id';
    $requerido = $requerido ?? false;
    $placeholder = $placeholder ?? 'Buscar por nombre o documento...';
@endphp
<label class="form-label">Cliente {{ $requerido ? '*' : '' }}</label>
<div class="position-relative">
    <input type="text" id="{{ $idInput }}_texto" class="form-control" autocomplete="off"
           placeholder="{{ $placeholder }}" value="{{ $clienteTexto ?? '' }}">
    <input type="hidden" name="{{ $nombreCampo }}" id="{{ $idInput }}_id" value="{{ $clienteId ?? '' }}" {{ $requerido ? 'required' : '' }}>
    <div id="{{ $idInput }}_resultados" class="list-group shadow-sm" style="display:none;position:absolute;z-index:1055;width:100%;max-height:240px;overflow-y:auto;"></div>
</div>

@once
    @push('scripts')
    <script>
    function iniciarBuscadorCliente(prefijo) {
        const texto = document.getElementById(prefijo + '_texto');
        const idInput = document.getElementById(prefijo + '_id');
        const resultados = document.getElementById(prefijo + '_resultados');
        if (! texto) return;
        let temporizador = null;

        texto.addEventListener('input', function () {
            idInput.value = '';
            clearTimeout(temporizador);
            const q = texto.value.trim();
            if (q.length < 2) {
                resultados.style.display = 'none';
                resultados.innerHTML = '';
                return;
            }
            temporizador = setTimeout(function () {
                fetch("{{ route('clientes.buscar') }}?q=" + encodeURIComponent(q))
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        resultados.innerHTML = '';
                        if (data.length === 0) {
                            resultados.innerHTML = '<div class="list-group-item text-spa-muted">Sin resultados</div>';
                        } else {
                            data.forEach(function (c) {
                                const item = document.createElement('button');
                                item.type = 'button';
                                item.className = 'list-group-item list-group-item-action';
                                const meta = [c.documento ? ('Doc: ' + c.documento) : null, c.telefono].filter(Boolean).join(' · ');
                                item.innerHTML = '<strong>' + c.nombre + '</strong>' + (meta ? '<br><small class="text-spa-muted">' + meta + '</small>' : '');
                                item.addEventListener('click', function () {
                                    idInput.value = c.id;
                                    texto.value = c.nombre;
                                    resultados.style.display = 'none';
                                });
                                resultados.appendChild(item);
                            });
                        }
                        resultados.style.display = 'block';
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (e.target !== texto && ! resultados.contains(e.target)) {
                resultados.style.display = 'none';
            }
        });
    }
    </script>
    @endpush
@endonce

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', function () { iniciarBuscadorCliente('{{ $idInput }}'); });</script>
@endpush
