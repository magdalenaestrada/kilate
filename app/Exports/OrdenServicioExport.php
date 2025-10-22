<?php

namespace App\Exports;

use App\Models\OrdenServicio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OrdenServicioExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OrdenServicio::with(['proveedor'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Codigo',
            'Fecha de Creación',
            'Cliente',
            'Proveedor',
            'Descripción',
            'Costo Final',
            'Moneda',
            'Estado',
        ];
    }

    public function map($orden): array
    {
        $estados = [
            'P' => 'Pendiente',
            'E' => 'En proceso',
            'F' => 'Completado',
            'A' => 'Anulado',
            'C' => 'Pagado',
        ];

        $simbolo = $orden->moneda === 'DOLARES' ? '$' : 'S/.';

        return [
            $orden->id,
            $orden->codigo,
            optional($orden->created_at)->format('d/m/Y'),
            optional($orden->cliente)->razon_social ?? '-',
            optional($orden->proveedor)->razon_social ?? '-',
            $orden->descripcion ?? '-',
            $simbolo . ' ' . number_format($orden->costo_final ?? 0, 2),
            $orden->moneda ?? '-',
            $estados[$orden->estado_servicio] ?? 'Desconocido',
        ];
    }
}
