<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OrdenServicioFullExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Órdenes de Servicio' => new OrdenServicioExport(),
            'Detalles de Órdenes' => new DetalleOrdenServicioExport(),
        ];
    }
}
