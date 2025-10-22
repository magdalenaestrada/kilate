<?php

namespace App\Exports;

use App\Models\DetalleOrdenServicio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DetalleOrdenServicioExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Incluye la orden asociada para mostrar su código o descripción
        return DetalleOrdenServicio::with('orden_servicio')->get();
    }

    public function headings(): array
    {
        return [
            'ID Detalle',
            'Código de Orden',
            'Descripción Orden',
            'Descripción Detalle',
            'Cantidad',
            'Precio Unitario',
            'Subtotal',
        ];
    }

    public function map($detalle): array
    {
        return [
            $detalle->id,
            optional($detalle->orden_servicio)->codigo ?? '-',
            optional($detalle->orden_servicio)->descripcion ?? '-',
            $detalle->descripcion ?? '-',
            $detalle->cantidad ?? 0,
            number_format($detalle->precio_unitario ?? 0, 2),
            number_format($detalle->subtotal ?? 0, 2),
        ];
    }
}
