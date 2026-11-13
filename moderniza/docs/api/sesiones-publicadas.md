# API REST · Sesiones publicadas de un evento

Servicio público de Moderniza para consultar el programa de un evento (requisito R6, criterio A5, condición AC6).
Lo consume la vista React `resources/js/pages/Public/Program.tsx`.

## Operación

| Método | Ruta | Autenticación | Límite |
|---|---|---|---|
| `GET` | `/api/events/{eventId}/sessions` | No (información pública) | 60 peticiones por minuto |

`eventId`: identificador numérico del evento de Attendize.

## Reglas

- Solo responde si el evento está **publicado en Attendize** (`events.is_live = 1`) y no está borrado.
- Solo devuelve sesiones **publicadas** (`event_sessions.is_published = 1`), ordenadas por hora de inicio.
- No publica datos internos: ni `account_id`, ni `event_id`, ni el estado de publicación, ni fechas de creación.

## Respuesta 200 OK

<!-- TODO T4-3: completa el ejemplo de respuesta con los campos que devuelve tu PublicSessionResource. -->

| Campo | Tipo | Descripción |
|---|---|---|
| `data[].id` | entero | Identificador de la sesión |
| `data[].title` | texto | Título (máx. 120) |
| `data[].description` | texto o `null` | Descripción opcional |
| `data[].starts_at` / `ends_at` | texto ISO 8601 | Inicio y fin con zona horaria |
| `data[].room` | texto | Sala |
| `data[].capacity` | entero | Aforo (1 a 5000) |
| `event` | objeto | `id` y `title` del evento |

Si el evento no tiene sesiones publicadas, `data` es una lista vacía `[]`.

## Errores

| Código | Cuándo | Cuerpo |
|---|---|---|
| `404 Not Found` | El evento no existe, está borrado o no está publicado en Attendize | `{ "message": "..." }` |
| `429 Too Many Requests` | Más de 60 peticiones por minuto desde la misma IP | `{ "message": "Too Many Attempts." }` |
| `500` | Error interno (se registra en `storage/logs`) | `{ "message": "Server Error" }` |

## Ejemplos de uso

```bash
curl -H "Accept: application/json" http://localhost:8000/api/events/1/sessions
```

```ts
const response = await fetch(`/api/events/${eventId}/sessions`, { headers: { Accept: 'application/json' } });
```

## Pruebas

`tests/Feature/PublicSessionsApiTest.php`: campos públicos, solo publicadas y ordenadas, 404 para evento no publicado o inexistente.
