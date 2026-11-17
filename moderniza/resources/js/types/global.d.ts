/**
 * Tipos de los datos que Laravel comparte con TODAS las páginas
 * (ver app/Http/Middleware/HandleInertiaRequests.php).
 */
export interface AuthUser {
    id: number;
    name: string;
    email: string;
}

export interface SharedProps {
    appName: string;
    auth: { user: AuthUser | null };
    flash: { success: string | null };
    [key: string]: unknown;
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: SharedProps;
    }
}
