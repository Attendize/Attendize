import '../css/app.css';
import { createInertiaApp } from '@inertiajs/react';
import type { ComponentType } from 'react';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'Moderniza';

// Cada página React vive en resources/js/pages/<Nombre>.tsx y Laravel la
// solicita con Inertia::render('<Nombre>', [...props]).
const pages = import.meta.glob<{ default: ComponentType }>('./pages/**/*.tsx', {
    eager: true,
});

void createInertiaApp({
    title: (title) => (title ? `${title} · ${appName}` : appName),
    resolve: (name) => {
        const page = pages[`./pages/${name}.tsx`];
        if (!page) {
            throw new Error(`No existe la página React "${name}" (resources/js/pages/${name}.tsx).`);
        }
        return page;
    },
    setup({ el, App, props }) {
        createRoot(el!).render(<App {...props} />);
    },
    progress: { color: '#1d4ed8' },
});
