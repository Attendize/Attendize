# Moderniza · Programa de sesiones de TechFest

Aplicación nueva que **convive con Attendize** sin modificarlo:

```
Navegador
 ├─ https://localhost:8081 → Attendize v2.8.0 (Laravel 6, Blade)   ← NO SE TOCA
 └─ http://localhost:8000  → Moderniza (Laravel 13 + Inertia 3 + React + TypeScript)
                                   │
                         MySQL "db" · base "attendize" (la misma)
```

- Moderniza **lee** las tablas de Attendize (`accounts`, `users`, `events`) y **crea solo una tabla nueva**: `event_sessions`.
- Para entrar en Moderniza se usa **el mismo usuario y contraseña** que en Attendize.
- Sus migraciones se registran en la tabla `moderniza_migrations` (Attendize sigue usando `migrations`).

> **Limitación conocida:** cada aplicación tiene su propio inicio de sesión. Si estás dentro de Attendize, tienes que iniciar sesión también en Moderniza.

---

## 1. Puesta en marcha (una sola vez)

Requisito: Attendize ya funciona con Docker (Tarea 1) y has creado en él al menos un organizador y un evento.

Desde la **carpeta raíz de tu repositorio de Attendize** (donde está `docker-compose.yml`):

```bash
# 1. Arrancar todo. docker-compose.override.yml añade los servicios de Moderniza.
docker compose up -d --build

# 2. Crear la base de datos de PRUEBAS (solo la primera vez)
#    Linux, macOS, WSL o Git Bash:
docker compose exec -T db mysql -uroot -pyes < moderniza/docker/crear-bd-pruebas.sql
#    Windows PowerShell (no admite "<"):
Get-Content moderniza/docker/crear-bd-pruebas.sql | docker compose exec -T db mysql -uroot -pyes

# 3. Comprobar los servicios
docker compose ps
```

| Servicio | Qué es | Dirección |
|---|---|---|
| `web` | Attendize (sin cambios) | https://localhost:8081 |
| `db` | MySQL compartido | puerto 3306 |
| `moderniza` | Laravel 13 (servidor) | http://localhost:8000 |
| `moderniza-vite` | Vite: compila React + TypeScript al guardar | puerto 5173 |

La primera vez `moderniza` tarda unos minutos (instala dependencias de Composer) y `moderniza-vite` también (instala las de npm). Puedes seguirlo con:

```bash
docker compose logs -f moderniza moderniza-vite
```

Abre http://localhost:8000 e inicia sesión con tu organizador de Attendize. Verás **Mis eventos** (ejemplo resuelto).

---

## 2. Qué te damos hecho y qué haces tú

**Hecho (no hace falta tocarlo, pero sí entenderlo):**

| Archivo | Para qué sirve |
|---|---|
| `app/Http/Controllers/EventController.php` + `resources/js/pages/Events/Index.tsx` | **Ejemplo resuelto** de recorrido React → Inertia → Laravel → MySQL (lectura). |
| `app/Http/Controllers/Auth/LoginController.php` + `pages/Auth/Login.tsx` | Acceso con los usuarios de Attendize. |
| `app/Models/User.php`, `Account.php`, `Event.php` | Modelos sobre las tablas de Attendize. |
| `app/Http/Middleware/HandleInertiaRequests.php` | Datos compartidos con todas las páginas (usuario, mensajes). |
| `resources/js/components/Field.tsx`, `Layout.tsx` | Componentes accesibles reutilizables. |
| `tests/Feature/ExampleEventsTest.php` | **Ejemplo resuelto** de pruebas. |

**Tarea 3: tú completas los `TODO`** (búscalos en tu editor: `TODO T3-`):

| TODO | Archivo | Qué hay que hacer | Criterio |
|---|---|---|---|
| T3-1 | `database/migrations/…_create_event_sessions_table.php` | Columnas y claves externas de `event_sessions` | A2 |
| T3-2 | `app/Models/EventSession.php` y `app/Models/Event.php` | `$fillable`, conversiones (`casts`) y relación `sessions()` | A2 |
| T3-3 | `app/Http/Requests/StoreEventSessionRequest.php` | Reglas de validación | A3 |
| T3-4 | `app/Http/Controllers/EventSessionController.php` · `findOwnEvent()` | Solo eventos de **tu** cuenta | A4 |
| T3-5 | `EventSessionController::store()` y `routes/web.php` | Guardar la sesión y su ruta POST | A2 |
| T3-6 | `resources/js/pages/Sessions/Create.tsx` | Campos que faltan, envío con Inertia y estado "Guardando…" | A2, A3, R4, R5 |
| T3-7 | `tests/Feature/CreateEventSessionTest.php` | Pruebas de datos inválidos y de otra cuenta | A3, A4 |

Orden recomendado: **T3-1 → T3-2 → T3-3 → T3-4 → T3-5 → T3-6 → T3-7**.

Mientras no completes un TODO, la aplicación te lo indica (por ejemplo, error `501 TODO T3-4`).

**Comprueba cada TODO nada más terminarlo** (no esperes al final). La guía completa, con pistas y errores típicos, está en `docs/GUIA_TAREA_3.md`.

| TODO | Cómo comprobarlo | Resultado esperado |
|---|---|---|
| T3-1 | `docker compose exec moderniza php artisan migrate` y después `php artisan test --group=t3-1` | `DONE` en la migración y `OK (3 tests…)` |
| T3-2 | `docker compose exec moderniza php artisan test --group=t3-2` | `OK (3 tests…)` |
| T3-3 | `docker compose exec moderniza php artisan test --group=t3-3` | `OK (2 tests…)` |
| T3-4 | `docker compose exec moderniza php artisan test --group=t3-4` | `OK (2 tests…)` y en el navegador se abre «Programa de sesiones» |
| T3-5 | `docker compose exec moderniza php artisan test --group=t3-5` | `OK (2 tests…)` |
| T3-6 | `docker compose exec moderniza-vite npm run types` y crear una sesión en el navegador | Sin errores de tipos; «Guardando…» al enviar y mensaje de éxito |
| T3-7 | `docker compose exec moderniza php artisan test` | Todas en verde y **ninguna** «incompleta» |

Las comprobaciones de `--group` están en `tests/Feature/PasoAPasoTest.php` (proporcionado: no lo modifiques). Si una sale en rojo, lee el mensaje: dice qué falta.

---

## 3. Órdenes que vas a usar

```bash
# Aplicar tu migración (T3-1) en la base "attendize"
docker compose exec moderniza php artisan migrate

# ¿Qué migraciones de Moderniza se han ejecutado?
docker compose exec moderniza php artisan migrate:status

# Si cambias la migración después de ejecutarla: deshacer y repetir
docker compose exec moderniza php artisan migrate:rollback
docker compose exec moderniza php artisan migrate

# Ver las rutas de Moderniza
docker compose exec moderniza php artisan route:list --except-vendor

# Ejecutar las pruebas (usan la base moderniza_test, NUNCA la de Attendize)
docker compose exec moderniza php artisan test

# Comprobar los tipos de TypeScript
docker compose exec moderniza-vite npm run types

# Ver la tabla nueva en MySQL
docker compose exec db mysql -uattendize -pattendize attendize -e "SELECT id, account_id, event_id, title, room, capacity FROM event_sessions;"
```

**Persistencia:** `docker compose stop` y `docker compose start` conservan los datos.
**No uses** `docker compose down -v`: `-v` borra el volumen de MySQL (Attendize y Moderniza).

---

## 4. Cómo comprobar que la Tarea 3 está terminada

1. `php artisan test` → todas las pruebas en verde (ninguna "incompleta").
2. Recorrido en el navegador: Mis eventos → Programa de sesiones → Nueva sesión →
   - formulario vacío: aparece un error junto a cada campo;
   - fin anterior al inicio y aforo 0: errores claros y **no se pierde lo escrito**;
   - datos correctos: mensaje "La sesión se ha creado correctamente" y la sesión aparece en la tabla.
3. Escribe a mano `http://localhost:8000/events/<id de un evento de OTRA cuenta>/sessions` → **404**.
4. `docker compose stop` + `docker compose start` → la sesión sigue ahí.
5. Teclado: con `Tab` se recorre todo el formulario, el foco se ve siempre y "Saltar al contenido" aparece al primer `Tab`.
6. Móvil (herramientas del navegador, 375 px): sin desplazamiento horizontal.

---

## 5. Estructura de Moderniza

```
moderniza/
├─ app/
│  ├─ Http/Controllers/      EventController (ejemplo) · EventSessionController (T3) · Auth/LoginController
│  ├─ Http/Requests/         StoreEventSessionRequest (T3)
│  ├─ Http/Middleware/       HandleInertiaRequests
│  └─ Models/                User · Account · Event (Attendize) · EventSession (nuevo)
├─ database/
│  ├─ migrations/            SOLO tablas nuevas de Moderniza
│  ├─ testing/attendize/     tablas mínimas de Attendize para la base de PRUEBAS
│  └─ factories/
├─ resources/js/
│  ├─ app.tsx                arranque de Inertia + React
│  ├─ components/            Field · Layout
│  ├─ pages/                 Auth/Login · Events/Index · Sessions/Index · Sessions/Create (T3)
│  └─ types/                 tipos TypeScript compartidos
├─ routes/web.php
├─ tests/Feature/            ExampleEventsTest (ejemplo) · CreateEventSessionTest (T3) · PasoAPasoTest (comprobaciones)
└─ docker/                   Dockerfile · entrypoint.sh · crear-bd-pruebas.sql
```

## 6. Versiones

PHP 8.3 · Laravel 13 · inertiajs/inertia-laravel 3 · @inertiajs/react 3 · React 19 · TypeScript 5 · Vite 8 · Tailwind CSS 4 · PHPUnit 12 · MySQL del servicio `db` de Attendize.

Moderniza es un trabajo derivado para uso educativo. Attendize se distribuye bajo la *Attribution Assurance License*: se conserva su atribución y su archivo `LICENSE` en la raíz del repositorio.
