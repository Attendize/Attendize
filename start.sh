#!/bin/bash

# manually load ENVs
if [ ! -f $PROJECT_PATH/.env ]; then
    chmod +x $PROJECT_PATH/generate_env.sh
    /bin/bash $PROJECT_PATH/generate_env.sh >> $PROJECT_PATH/.env
    chown -R $APACHE_RUN_USER:$APACHE_RUN_GROUP $PROJECT_PATH/.env

    # if we don't have APP_KEY in envs, lets generate a new one
    if [[ -z "${APP_KEY}" ]]; then
        php artisan key:generate
        # update APP_KEY ENV with what artisan has generated
        export $(grep APP_KEY $PROJECT_PATH/.env)
        php artisan config:cache
    fi
fi

if [[ ! -v WORKER ]]; then
    exec /usr/sbin/apache2ctl -D FOREGROUND
else
    exec php artisan queue:work --daemon
fi
