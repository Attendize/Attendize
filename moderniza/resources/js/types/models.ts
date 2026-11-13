/** Tipos de los datos que envían los controladores a cada página. */

export interface EventSummary {
    id: number;
    title: string;
    /** Publicado en Attendize (Tarea 4). */
    is_live?: boolean;
}

export interface EventListItem extends EventSummary {
    start_date: string | null;
    end_date: string | null;
    is_live: boolean;
}

export interface SessionListItem {
    id: number;
    title: string;
    starts_at: string;
    ends_at: string;
    room: string;
    capacity: number;
    /** Tarea 4: visible en el programa público. */
    is_published: boolean;
}

/** Tarea 4 · Una sesión tal como la devuelve la API REST pública. */
export interface PublicSession {
    id: number;
    title: string;
    description: string | null;
    /** Fecha y hora en formato ISO 8601. */
    starts_at: string;
    ends_at: string;
    room: string;
    capacity: number;
}

/** Tarea 4 · Respuesta completa de GET /api/events/{id}/sessions */
export interface PublicSessionsResponse {
    data: PublicSession[];
    event: { id: number; title: string };
}
