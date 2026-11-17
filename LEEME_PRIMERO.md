# Tarea 3 · Plantilla Moderniza

1. Trabaja en tu rama `tarea-03-mejora-completa`.
2. Descomprime este paquete en la **raíz de tu repositorio de Attendize** (la carpeta donde está `docker-compose.yml`). Quedará así:

   ```
   Attendize/
   ├─ docker-compose.yml            (de Attendize, NO lo modifiques)
   ├─ docker-compose.override.yml   (NUEVO: añade los servicios de Moderniza)
   ├─ moderniza/                    (NUEVO: la aplicación modernizada)
   └─ ... resto de Attendize sin cambios
   ```

3. Haz un commit **solo con la plantilla**, antes de cambiar nada:

   ```bash
   git add docker-compose.override.yml moderniza
   git commit -m "T3: base Moderniza proporcionada por el profesorado"
   git tag tarea-03-inicio
   git push origin tarea-03-mejora-completa tarea-03-inicio
   ```

4. Sigue `moderniza/README.md`: puesta en marcha y lista de `TODO T3-1` a `TODO T3-7`.
