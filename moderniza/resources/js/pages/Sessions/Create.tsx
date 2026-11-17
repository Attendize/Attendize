import { Link, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Field from '../../components/Field';
import Layout from '../../components/Layout';
import type { EventSummary } from '../../types/models';

interface Props {
    event: EventSummary;
}

/** Datos del formulario. Los nombres coinciden con los campos que valida Laravel. */
interface SessionForm {
    title: string;
    description: string;
    starts_at: string;
    ends_at: string;
    room: string;
    capacity: string;
}

/**
 * Formulario "Nueva sesión" (Tarea 3).
 * React recoge los datos, Inertia los envía a Laravel y, si Laravel
 * devuelve errores, Inertia los deja en `errors` sin perder lo escrito.
 */
export default function SessionsCreate({ event }: Props) {
    const { data, setData, post, processing, errors } = useForm<SessionForm>({
        title: '',
        description: '',
        starts_at: '',
        ends_at: '',
        room: '',
        capacity: '',
    });

    function submit(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();
        // TODO T3-6: envía el formulario con Inertia a POST /events/{id}/sessions.
        // Pista: usa post(...) de useForm. Conserva la posición (preserveScroll).
    }

    return (
        <Layout title={`Nueva sesión · ${event.title}`}>
            {/* noValidate: la validación de verdad la hace Laravel (requisito R2/R3). */}
            <form onSubmit={submit} noValidate className="card max-w-xl">
                <p className="mb-4 text-sm text-slate-700">Todos los campos son obligatorios salvo la descripción.</p>

                {/* EJEMPLO RESUELTO: así se conecta un campo con el estado y con su error. */}
                <Field label="Título" id="title" error={errors.title}>
                    <input
                        type="text"
                        maxLength={120}
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                    />
                </Field>

                <Field label="Descripción (opcional)" id="description" error={errors.description}>
                    <textarea
                        rows={3}
                        value={data.description}
                        onChange={(e) => setData('description', e.target.value)}
                    />
                </Field>

                {/* TODO T3-6: añade los campos Inicio (starts_at) y Fin (ends_at) con type="datetime-local". */}

                {/* TODO T3-6: añade los campos Sala (room, texto) y Aforo (capacity, type="number"). */}

                <div className="flex flex-wrap gap-3">
                    {/* TODO T3-6: mientras se envía (processing), desactiva el botón y muestra "Guardando…". */}
                    <button type="submit" className="btn-primary">Guardar sesión</button>
                    <Link href={`/events/${event.id}/sessions`} className="btn-secondary">
                        Cancelar
                    </Link>
                </div>
            </form>
        </Layout>
    );
}
