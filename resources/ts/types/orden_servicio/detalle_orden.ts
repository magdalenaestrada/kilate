import { GenericStatus } from "../../enums/status/generic_status";

export type OrdenServicio = {
    id: number;
    codigo: string;
    descripcion: string;
    estado: GenericStatus;
    created_at: string | null;
    updated_at: string | null;
    deleted_at: string | null;
};
