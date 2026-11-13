<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

/**
 * Datos que Inertia comparte con TODAS las páginas React.
 */
class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    /**
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'appName' => config('app.name'),
            // Solo los datos mínimos del usuario: nunca la contraseña ni el token.
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->fullName(),
                    'email' => $user->email,
                ] : null,
            ],
            // Mensajes de confirmación enviados con ->with('success', ...)
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
        ];
    }
}
