@forelse ($ordenes as $orden)
    <tr>
        <td>{{ $orden->id }}</td>
        <td>{{ $orden->created_at->format('d/m/Y') }}</td>
        <td>{{ $orden->proveedor->razon_social ?? '-' }}</td>
        <td>{{ $orden->descripcion ?? '-' }}</td>
        <td>S/. {{ number_format($orden->costo_final, 2) }}</td>
        <td>
            <span class="badge 
                @if ($orden->estado == 'PENDIENTE') bg-warning
                @elseif ($orden->estado == 'COMPLETADO') bg-success
                @else bg-secondary @endif">
                {{ $orden->estado }}
            </span>
        </td>
        <td>
            <a href="{{ route('orden-servicio.show', $orden->id) }}" class="btn btn-primary btn-sm">Ver</a>
            <a href="{{ route('orden-servicio.edit', $orden->id) }}" class="btn btn-warning btn-sm">Editar</a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center text-muted">No se encontraron resultados.</td>
    </tr>
@endforelse
