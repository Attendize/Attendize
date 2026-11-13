import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import Field from '../../components/Field';

/** Inicio de sesión con un usuario de Attendize (proporcionado). */
export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function submit(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();
        post('/login', { onFinish: () => setData('password', '') });
    }

    return (
        <>
            <Head title="Acceso" />
            <main id="contenido" className="mx-auto max-w-sm px-4 py-12">
                <h1 className="mb-6 text-2xl font-bold text-slate-900">Acceso a la gestión</h1>
                <form onSubmit={submit} noValidate>
                    <Field label="Correo electrónico" id="email" error={errors.email}>
                        <input
                            type="email"
                            autoComplete="username"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            autoFocus
                        />
                    </Field>
                    <Field label="Contraseña" id="password" error={errors.password}>
                        <input
                            type="password"
                            autoComplete="current-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                        />
                    </Field>
                    <label className="mb-4 flex items-center gap-2">
                        <input
                            type="checkbox"
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                        />
                        Recordarme
                    </label>
                    <button type="submit" className="btn-primary w-full" disabled={processing}>
                        {processing ? 'Entrando…' : 'Entrar'}
                    </button>
                </form>
            </main>
        </>
    );
}
