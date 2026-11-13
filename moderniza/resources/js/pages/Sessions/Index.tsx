import { Link } from '@inertiajs/react';
import Layout from '../../components/Layout';
import type { EventSummary, SessionListItem } from '../../types/models';

interface Props {
    event: EventSummary;
    sessions: SessionListItem[];
}

/** Programa de sesiones de un evento propio (proporcionado). */
export default function SessionsIndex({ event, sessions }: Props) {
    return (
        <Layout title={`Programa · ${event.title}`}>
            <div className="mb-4 flex flex-wrap gap-3">
                <Link href={`/events/${event.id}/sessions/create`} className="btn-primary">
                    Nueva sesión
                </Link>
                <Link href="/events" className="btn-secondary">
                    Volver a mis eventos
                </Link>
            </div>

            {sessions.length === 0 ? (
                <p>Este evento todavía no tiene sesiones.</p>
            ) : (
                <div className="overflow-x-auto">
                    <table className="w-full border-collapse text-left">
                        <caption className="sr-only">Sesiones de {event.title}</caption>
                        <thead>
                            <tr className="border-b border-slate-300">
                                <th scope="col" className="py-2 pr-3">Sesión</th>
                                <th scope="col" className="py-2 pr-3">Horario</th>
                                <th scope="col" className="py-2 pr-3">Sala</th>
                                <th scope="col" className="py-2">Aforo</th>
                            </tr>
                        </thead>
                        <tbody>
                            {sessions.map((s) => (
                                <tr key={s.id} className="border-b border-slate-200">
                                    <td className="py-2 pr-3">{s.title}</td>
                                    <td className="py-2 pr-3">{s.starts_at}–{s.ends_at}</td>
                                    <td className="py-2 pr-3">{s.room}</td>
                                    <td className="py-2">{s.capacity}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            )}
        </Layout>
    );
}
