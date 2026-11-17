import { Link } from '@inertiajs/react';
import Layout from '../../components/Layout';
import type { EventListItem } from '../../types/models';

interface Props {
    events: EventListItem[];
}

/**
 * EJEMPLO RESUELTO: listado de los eventos de MI cuenta.
 * Los datos llegan como props desde EventController::index().
 */
export default function EventsIndex({ events }: Props) {
    return (
        <Layout title="Mis eventos">
            {events.length === 0 ? (
                <p>Todavía no hay eventos en tu cuenta. Créalos desde Attendize.</p>
            ) : (
                <ul className="space-y-3">
                    {events.map((event) => (
                        <li key={event.id} className="card">
                            <h2 className="text-lg font-semibold">{event.title}</h2>
                            <p className="text-sm text-slate-700">
                                {event.start_date ?? 'Sin fecha'}
                                {event.end_date && ` – ${event.end_date}`}
                                {' · '}
                                <span>{event.is_live ? 'Publicado' : 'No publicado'}</span>
                            </p>
                            <div className="mt-2 flex flex-wrap gap-4">
                                <Link href={`/events/${event.id}/sessions`} className="link">
                                    Programa de sesiones<span className="sr-only"> de {event.title}</span>
                                </Link>
                                {/* Tarea 4: vista pública, solo para eventos publicados en Attendize */}
                                {event.is_live && (
                                    <Link href={`/eventos/${event.id}/programa`} className="link">
                                        Programa público<span className="sr-only"> de {event.title}</span>
                                    </Link>
                                )}
                            </div>
                        </li>
                    ))}
                </ul>
            )}
        </Layout>
    );
}
