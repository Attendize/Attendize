import { Head, Link, router, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';

interface Props {
    title: string;
    children: ReactNode;
}

/**
 * Estructura común: enlace para saltar al contenido, cabecera,
 * mensaje de confirmación (flash) y contenido principal.
 */
export default function Layout({ title, children }: Props) {
    const { appName, auth, flash } = usePage().props;

    return (
        <>
            <Head title={title} />
            <a href="#contenido" className="skip-link">
                Saltar al contenido
            </a>

            <header className="border-b border-slate-200 bg-white">
                <div className="mx-auto flex max-w-4xl flex-wrap items-center justify-between gap-3 px-4 py-3">
                    <Link href="/events" className="text-lg font-semibold text-slate-900">
                        {appName}
                    </Link>
                    {auth.user && (
                        <div className="flex items-center gap-3 text-sm">
                            <span className="text-slate-700">{auth.user.name}</span>
                            <button
                                type="button"
                                className="btn-secondary"
                                onClick={() => router.post('/logout')}
                            >
                                Cerrar sesión
                            </button>
                        </div>
                    )}
                </div>
            </header>

            <main id="contenido" className="mx-auto max-w-4xl px-4 py-6">
                <h1 className="mb-4 text-2xl font-bold text-slate-900">{title}</h1>

                {flash.success && (
                    <p role="status" className="mb-4 rounded border border-green-700 bg-green-50 p-3 text-green-900">
                        ✔ {flash.success}
                    </p>
                )}

                {children}
            </main>
        </>
    );
}
