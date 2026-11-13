/** Tipos de los datos que envían los controladores a cada página. */

export interface EventSummary {
    id: number;
    title: string;
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
}
