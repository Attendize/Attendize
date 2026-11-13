import { router } from '@inertiajs/react';
import { useState } from 'react';

interface Props {
    eventId: number;
    sessionId: number;
    sessionTitle: string;
    isPublished: boolean;
}

/**
 * Tarea 4 (proporcionado) · Botón para publicar o retirar una sesión.
 * Envía el estado DESEADO con PATCH; Laravel comprueba permisos y guarda.
 */
export default function PublishButton({ eventId, sessionId, sessionTitle, isPublished }: Props) {
    const [processing, setProcessing] = useState(false);

    function change() {
        router.patch(
            `/events/${eventId}/sessions/${sessionId}/publication`,
            { is_published: !isPublished },
            {
                preserveScroll: true,
                onStart: () => setProcessing(true),
                onFinish: () => setProcessing(false),
            },
        );
    }

    return (
        <button type="button" className="btn-secondary" onClick={change} disabled={processing}>
            {processing ? 'Guardando…' : isPublished ? 'Retirar' : 'Publicar'}
            <span className="sr-only"> la sesión {sessionTitle}</span>
        </button>
    );
}
