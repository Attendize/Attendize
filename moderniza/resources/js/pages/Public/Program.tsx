import { useCallback, useEffect, useState } from 'react';
import Layout from '../../components/Layout';
import type { EventSummary, PublicSession, PublicSessionsResponse } from '../../types/models';

interface Props {
    event: EventSummary;
}

/** Estados posibles de la carga de datos desde la API. */
type LoadState =
    | { status: 'loading' }
    | { status: 'error'; message: string }
    | { status: 'ready'; sessions: PublicSession[] };

const dayFormat = new Intl.DateTimeFormat('es-ES', { weekday: 'long', day: 'numeric', month: 'long' });
const timeFormat = new Intl.DateTimeFormat('es-ES', { hour: '2-digit', minute: '2-digit' });

/** Agrupa las sesiones por día para que el programa se lea como una agenda. */
function groupByDay(sessions: PublicSession[]): [string, PublicSession[]][] {
    const groups = new Map<string, PublicSession[]>();
    for (const session of sessions) {
        const day = dayFormat.format(new Date(session.starts_at));
        groups.set(day, [...(groups.get(day) ?? []), session]);
    }
    return [...groups.entries()];
}

/**
 * Tarea 4 · Programa PÚBLICO de un evento.
 * Laravel solo envía el evento; las sesiones se piden de forma ASÍNCRONA
 * a la API REST: GET /api/events/{id}/sessions (R6, A5, Cliente RA7).
 */
export default function PublicProgram({ event }: Props) {
    const [state, setState] = useState<LoadState>({ status: 'loading' });

    const loadSessions = useCallback(async (signal?: AbortSignal) => {
        setState({ status: 'loading' });
        // TODO T4-4: pide las sesiones a la API con fetch() y actualiza el estado.
        // 1. fetch(`/api/events/${event.id}/sessions`, { headers: { Accept: 'application/json' }, signal })
        // 2. Si la respuesta no es ok (response.ok === false) -> estado 'error' con un mensaje comprensible.
        // 3. Si es ok -> const body: PublicSessionsResponse = await response.json(); estado 'ready'.
        // 4. Si falla la red (catch) -> estado 'error'. Ignora el error si se ha abortado (signal.aborted).
    }, [event.id]);

    useEffect(() => {
        const controller = new AbortController();
        void loadSessions(controller.signal);
        return () => controller.abort(); // si se abandona la página, se cancela la petición
    }, [loadSessions]);

    return (
        <Layout title={`Programa · ${event.title}`}>
            {/* aria-live: los lectores de pantalla anuncian los cambios de estado */}
            <div aria-live="polite" aria-busy={state.status === 'loading'}>
                {state.status === 'loading' && <p>Cargando programa…</p>}

                {state.status === 'error' && (
                    <div role="alert" className="rounded border border-red-700 bg-red-50 p-3 text-red-900">
                        <p>⚠ {state.message}</p>
                        <button type="button" className="btn-secondary mt-2" onClick={() => void loadSessions()}>
                            Reintentar
                        </button>
                    </div>
                )}

                {state.status === 'ready' && state.sessions.length === 0 && (
                    <p>Todavía no hay sesiones publicadas en este programa.</p>
                )}

                {state.status === 'ready' &&
                    groupByDay(state.sessions).map(([day, sessions], i) => (
                        <section key={day} className="mb-6" aria-labelledby={`dia-${i}`}>
                            <h2 id={`dia-${i}`} className="mb-2 text-xl font-semibold first-letter:uppercase">
                                {day}
                            </h2>
                            <ul className="space-y-3">
                                {sessions.map((s) => (
                                    <li key={s.id} className="card">
                                        <h3 className="text-lg font-semibold">{s.title}</h3>
                                        <p className="text-slate-700">
                                            <time dateTime={s.starts_at}>{timeFormat.format(new Date(s.starts_at))}</time>
                                            {' – '}
                                            <time dateTime={s.ends_at}>{timeFormat.format(new Date(s.ends_at))}</time>
                                            {' · '}
                                            {s.room} · {s.capacity} plazas
                                        </p>
                                        {s.description && <p className="mt-1">{s.description}</p>}
                                    </li>
                                ))}
                            </ul>
                        </section>
                    ))}
            </div>
        </Layout>
    );
}
