# Tarea 4 · Plantilla Moderniza

1. Trabaja desde tu entrega de la Tarea 3:

   ```bash
   git switch -c tarea-04-modernizacion tarea-03-entrega
   ```

2. Descomprime este paquete en la **raíz de tu repositorio** (donde está `docker-compose.yml`). Acepta sustituir los archivos que te pida: solo son archivos base del profesorado. **No sobrescribe tu trabajo de la Tarea 3.**
3. Haz un commit solo con la plantilla y crea la etiqueta de inicio:

   ```bash
   git add -A
   git commit -m "T4: plantilla proporcionada por el profesorado"
   git tag tarea-04-inicio
   git push -u origin tarea-04-modernizacion
   git push origin tarea-04-inicio
   docker compose exec moderniza php artisan migrate
   ```

4. Sigue `moderniza/docs/tarea-04/README.md`: lista de `TODO T4-1` a `T4-5` y cambios que haces tú en tu código.
