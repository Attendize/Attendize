import type { ReactElement } from 'react';
import { cloneElement } from 'react';

interface Props {
    /** Texto de la etiqueta visible. */
    label: string;
    /** Identificador del campo: se usa en htmlFor, id y en el id del error. */
    id: string;
    /** Mensaje de error que devuelve Laravel (si lo hay). */
    error?: string;
    /** Texto de ayuda opcional. */
    hint?: string;
    /** El control del formulario: <input>, <textarea>... */
    children: ReactElement<Record<string, unknown>>;
}

/**
 * Campo de formulario accesible y reutilizable:
 * - la etiqueta está asociada al control (htmlFor / id);
 * - el error queda vinculado con aria-describedby y aria-invalid;
 * - el error no depende solo del color (lleva texto e icono).
 */
export default function Field({ label, id, error, hint, children }: Props) {
    const hintId = hint ? `${id}-hint` : undefined;
    const errorId = error ? `${id}-error` : undefined;
    const describedBy = [hintId, errorId].filter(Boolean).join(' ') || undefined;

    return (
        <div className="mb-4">
            <label htmlFor={id} className="mb-1 block font-medium text-slate-900">
                {label}
            </label>
            {hint && (
                <p id={hintId} className="mb-1 text-sm text-slate-600">
                    {hint}
                </p>
            )}
            {cloneElement(children, {
                id,
                name: id,
                'aria-invalid': error ? true : undefined,
                'aria-describedby': describedBy,
                className: `input ${error ? 'input-error' : ''}`,
            })}
            {error && (
                <p id={errorId} className="mt-1 text-sm font-medium text-red-800">
                    ⚠ {error}
                </p>
            )}
        </div>
    );
}
