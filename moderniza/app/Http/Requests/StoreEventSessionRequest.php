<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación de la creación de una sesión.
 *
 * Aquí solo se valida el FORMATO de los datos. La autorización
 * (¿el evento es de mi cuenta?) se comprueba en el controlador.
 */
class StoreEventSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // La pertenencia del evento a la cuenta se comprueba en el controlador.
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // TODO T3-3: reglas de validación (criterio A3).
        // title: obligatorio, texto, máx. 120 · description: opcional, texto, máx. 5000
        // starts_at: obligatoria, fecha · ends_at: obligatoria, fecha, POSTERIOR a starts_at
        // room: obligatoria, texto, máx. 80 · capacity: obligatorio, entero, entre 1 y 5000
        return [];
    }

    /**
     * Mensajes comprensibles para el usuario (requisito R4).
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Escribe el título de la sesión.',
            'title.max' => 'El título no puede superar los 120 caracteres.',
            'starts_at.required' => 'Indica la fecha y hora de inicio.',
            'starts_at.date' => 'La fecha de inicio no es válida.',
            'ends_at.required' => 'Indica la fecha y hora de fin.',
            'ends_at.date' => 'La fecha de fin no es válida.',
            'ends_at.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'room.required' => 'Indica la sala.',
            'capacity.required' => 'Indica el aforo.',
            'capacity.integer' => 'El aforo debe ser un número entero.',
            'capacity.min' => 'El aforo debe ser al menos 1.',
            'capacity.max' => 'El aforo no puede superar 5000.',
        ];
    }
}
