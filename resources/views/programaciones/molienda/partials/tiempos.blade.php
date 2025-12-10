@forelse($tiempos as $t)
    <tr>
        <td>{{ $t->fecha_inicio }}</td>
        <td>{{ $t->hora_inicio }}</td>
        <td>{{ $t->fecha_fin }}</td>
        <td>{{ $t->hora_fin }}</td>
        <td>{{ $t->tonelaje }}</td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="text-muted">No hay tiempos registrados...
        </td>
    </tr>
@endforelse
