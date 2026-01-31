<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesosExport implements FromCollection, WithHeadings, WithStyles
{
    protected $pesos;

    public function __construct($pesos)
    {
        $this->pesos = collect($pesos);
    }

    public function collection()
    {
        if ($this->pesos->isEmpty()) {
            return collect([$this->filaVacia('No hay registros para exportar')]);
        }

        $collection = $this->pesos->map(function ($peso) {
            return [
                'NroSalida'      => $peso->NroSalida,
                'Horas'          => $peso->Horas,
                'Fechas'         => $peso->Fechas,
                'Fechai'         => $peso->Fechai,
                'Horai'          => $peso->Horai,
                'Pesoi'          => $peso->Pesoi,
                'Pesos'          => $peso->Pesos,
                'Bruto'          => $peso->Bruto,
                'Tara'           => $peso->Tara,
                'Neto'           => $peso->Neto,
                'Placa'          => $peso->Placa,
                'Observacion'    => $peso->Observacion,
                'Producto'       => $peso->Producto,
                'Conductor'      => $peso->Conductor,
                'Transportista'  => $peso->Transportista,
                'RazonSocial'    => $peso->RazonSocial,
                'Operadori'      => $peso->Operadori,
                'Destarado'      => $peso->Destarado,
                'Operadors'      => $peso->Operadors,
                'carreta'        => $peso->carreta,
                'guia'           => $peso->guia,
                'guiat'          => $peso->guiat,
                'destino'        => $peso->destino,
                'origen'         => $peso->origen,
                'pesaje'         => $peso->pesaje,
                'Estado'         => optional($peso->estado)->nombre ?? 'SIN ASIGNAR',
                'Lote'           => optional($peso->lote)->nombre ?? 'SIN ASIGNAR',
            ];
        });

        // 🔥 FILA TOTAL
        $totalNeto = $collection->sum('Neto');
        $collection->prepend($this->filaTotal($totalNeto));

        return $collection;
    }

    private function filaTotal($totalNeto)
    {
        return [
            'NroSalida'      => 'TOTAL',
            'Horas'          => '',
            'Fechas'         => '',
            'Fechai'         => '',
            'Horai'          => '',
            'Pesoi'          => '',
            'Pesos'          => '',
            'Bruto'          => '',
            'Tara'           => '',
            'Neto'           => $totalNeto,
            'Placa'          => '',
            'Observacion'    => '',
            'Producto'       => '',
            'Conductor'      => '',
            'Transportista'  => '',
            'RazonSocial'    => '',
            'Operadori'      => '',
            'Destarado'      => '',
            'Operadors'      => '',
            'carreta'        => '',
            'guia'           => '',
            'guiat'          => '',
            'destino'        => '',
            'origen'         => '',
            'pesaje'         => '',
            'Estado'         => '',
            'Lote'           => '',
        ];
    }

    private function filaVacia($mensaje)
    {
        return array_merge($this->filaTotal(0), [
            'Observacion' => $mensaje,
            'Estado'      => $mensaje,
        ]);
    }

    public function headings(): array
    {
        return [
            'NroSalida','Horas','Fechas','Fechai','Horai','Pesoi','Pesos','Bruto','Tara','Neto',
            'Placa','Observacion','Producto','Conductor','Transportista','RazonSocial',
            'Operadori','Destarado','Operadors','Carreta','Guia','Guiat','Destino','Origen','Pesaje','Estado','Lote'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
            ],
            2 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFC000'],
                ],
            ],
        ];
    }
}
