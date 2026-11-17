# Tarea 4 · Núcleo del programa y cambio de requisito

Partes de **tu** entrega de la Tarea 3 (`tarea-03-entrega`). Esta plantilla **solo añade archivos nuevos** y sustituye algunos archivos base que no tenías que modificar. **No sobrescribe tu trabajo de la Tarea 3.**

## Qué se añade en esta tarea

| Función | Dónde se ve |
|---|---|
| Publicar o retirar una sesión | Programa de sesiones (gestión), columna *Estado* y botón *Publicar / Retirar* |
| Programa público en React | `http://localhost:8000/eventos/{id}/programa`, sin iniciar sesión |
| API REST de sesiones publicadas | `GET http://localhost:8000/api/events/{id}/sessions` (JSON) |
| **Cambio de requisito** | No se puede crear una sesión si su sala está ocupada en ese horario |

**Petición de cambio (literal):**
> «Dos sesiones del mismo evento no pueden coincidir en la misma sala en horarios que se solapen.»

## 1. Incorporar la plantilla

```bash
git switch -c tarea-04-modernizacion tarea-03-entrega
# Descomprime la plantilla en la raíz del repositorio (acepta sustituir los archivos que indica)
git add -A
git commit -m "T4: plantilla proporcionada por el profesorado"
git tag tarea-04-inicio
git push -u origin tarea-04-modernizacion
git push origin tarea-04-inicio

# Aplicar la migración nueva (índice para comprobar solapes)
docker compose exec moderniza php artisan migrate
```

Archivos que **sustituye** (eran base del profesorado): `bootstrap/app.php`, `config/app.php`, `.env.example`, `resources/js/types/models.ts`, `resources/js/pages/Events/Index.tsx` y `tests/TestCase.php`.

## 2. TODO de la Tarea 4

| TODO | Archivo | Qué hay que hacer |
|---|---|---|
| T4-1 | `app/Rules/NoRoomOverlap.php` | **El cambio:** detectar sesiones que se solapan en la misma sala |
| T4-2 | `app/Http/Controllers/SessionPublicationController.php` | Publicar o retirar una sesión de **tu** cuenta |
| T4-3 | `app/Http/Controllers/Api/PublicSessionController.php`, `app/Http/Resources/PublicSessionResource.php`, `docs/api/sesiones-publicadas.md` | API REST: solo sesiones publicadas y solo campos públicos |
| T4-4 | `resources/js/pages/Public/Program.tsx` | Pedir las sesiones a la API con `fetch` y gestionar los estados cargando / error / vacío |
| T4-5 | `tests/Feature/SessionOverlapTest.php`, `tests/Feature/PublicSessionsApiTest.php` | Pruebas de los casos límite del cambio y de la API |

## 3. Cambios que haces TÚ en tu código de la Tarea 3

Estos archivos son tuyos y la plantilla no los toca. Haz estos cambios a mano (búscalos luego en la solución con el comentario `T4 · EDITA TÚ`).

**a) `app/Http/Requests/StoreEventSessionRequest.php`**: aplica la regla del cambio a la sala:

```php
use App\Rules\NoRoomOverlap;
// ...
'room' => [
    'required', 'string', 'max:80',
    new NoRoomOverlap((int) $this->route('eventId'), (int) $this->user()->account_id),
],
```

> ¿Por qué se pasa la cuenta del usuario? Para que la regla **solo consulte sesiones de tu cuenta**. Si no, un usuario podría averiguar si una sala de otra cuenta está ocupada.

**b) `app/Models/EventSession.php`**: en `casts()`, añade `'is_published' => 'boolean'`.

**c) `app/Http/Controllers/EventSessionController.php`**, método `index()`:
- en `'event' => [...]` añade `'is_live' => $event->is_live`;
- en cada sesión añade `'is_published' => $session->is_published`.

**d) `resources/js/pages/Sessions/Index.tsx`**:
- importa `PublishButton` desde `../../components/PublishButton`;
- añade las columnas **Estado** (texto «Publicada» / «Borrador», no solo color) y **Acción** (el botón);
- si `event.is_live`, añade un enlace «Ver programa público» a `/eventos/{id}/programa`.

## 4. Documentación que entregas

| Archivo | Contenido |
|---|---|
| `docs/tarea-04/01-analisis-cambio.md` | Análisis del cambio: **ya tienes la plantilla** |
| `docs/tarea-04/02-requisitos-actualizados.md` | Requisitos y criterios de aceptación tras el cambio |
| `docs/tarea-04/03-pruebas-regresion.md` | Pruebas nuevas y de regresión con resultados |
| `docs/tarea-04/04-evidencias.md` | Capturas y enlaces: núcleo, cambio, errores, permisos, móvil y persistencia |
| `docs/api/sesiones-publicadas.md` | Documentación de la API (completa el TODO T4-3) |

## 5. Cómo comprobar que has terminado

1. `docker compose exec moderniza php artisan test`: **todas** en verde, también las de la Tarea 3 (regresión).
2. Creas una sesión en *Aula 1* de 10:00 a 11:00 y otra en la misma sala de 10:30 a 11:30: aparece el error junto al campo *Sala* y no se guarda.
3. De 11:00 a 12:00 en *Aula 1*: **sí** se guarda (empieza cuando termina la otra).
4. Publicas una sesión: aparece en `/eventos/{id}/programa` sin iniciar sesión, en móvil y escritorio.
5. Retiras la sesión: desaparece del programa y de la API.
6. `curl -H "Accept: application/json" http://localhost:8000/api/events/{id}/sessions`: el JSON no contiene `account_id`.
7. La API de un evento **no publicado** en Attendize responde `404`.
8. `docker compose exec moderniza php artisan migrate:rollback` deshace la migración del índice sin perder sesiones. Vuelve a aplicarla con `migrate`.
