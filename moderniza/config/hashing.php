<?php

/*
 * Moderniza comparte la tabla "users" con Attendize.
 * Desactivamos el "rehash" automático al iniciar sesión para que entrar en
 * Moderniza NO modifique las contraseñas guardadas por Attendize.
 * (El resto de opciones de hashing son las predeterminadas de Laravel.)
 */
return [
    'rehash_on_login' => false,
];
