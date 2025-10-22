<?php

namespace App\Enums\Models;

enum EstadosProcesosEnum: String
{
    case PENDIENTE = "P";
    case PROCESO = "E";
    case FINALIZADO = "F";
    case ANULADO = "A";
    case PAGADO = "C";
}
