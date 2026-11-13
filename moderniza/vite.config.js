import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.tsx'],
            refresh: true,
        }),
        react(),
        tailwindcss(),
    ],
    server: {
        // Necesario dentro de Docker: escuchar en todas las interfaces
        // y que el navegador cargue los recursos desde localhost:5173.
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: { host: 'localhost' },
        watch: {
            // Sondeo: necesario para detectar cambios en carpetas de Windows
            // montadas en Docker (los eventos del sistema no llegan al contenedor).
            usePolling: true,
            interval: 300,
            ignored: ['**/storage/**', '**/vendor/**', '**/node_modules/**'],
        },
    },
});
