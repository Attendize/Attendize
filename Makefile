# Commonly used commands, compatible with Linux type shells

# build the docker images for any environment
build:
	docker build --tag attendize_base --target base .
	docker build --tag attendize_worker --target worker --cache-from attendize_base:latest .
	docker build --tag attendize_web --target web --cache-from attendize_worker:latest .

build-apache:
	docker build --tag attendize_base --target base --file Dockerfile-apache .
	docker build --tag attendize_worker --target worker --cache-from attendize_base:latest --file Dockerfile-apache .
	docker build --tag attendize_web --target web --cache-from attendize_worker:latest --file Dockerfile-apache .

################
# The following commands are for local development use only and won't work in a production environment
################

# set up docker images and run containers for local development with docker-compose only
setup: build
	cp .env.example .env
	docker-compose up -d
	docker-compose exec web sh -c 'wait-for-it db:3306 -t 180 && php artisan key:generate && php artisan migrate'
	docker-compose up -d
	docker-compose exec web sh -c 'wait-for-it web:443 -t 120'
	open https://localhost:8081/install
	docker-compose exec web tail -f /var/log/nginx/access.log /var/log/nginx/error.log /var/log/php-fpm.log storage/logs/*

# run the whole stack and open up the app in the browser
run:
	docker-compose up -d
	docker-compose exec web sh -c 'wait-for-it db:3306 -t 180'
	docker-compose exec web sh -c 'wait-for-it web:443 -t 120'
	open https://localhost:8081/

# open a bash prompt on a running web container
shell:
	docker-compose exec web /bin/bash

# run the unit tests on a running web container
test:
	docker-compose exec web bash -c "touch database/database.sqlite && vendor/bin/phpunit"

# clear all laravel caches on a running web container
cache:
	docker-compose exec web php artisan optimize:clear

# clear and recompile the autoloder files, for example if you add a new class file
autoload:
	docker-compose exec web composer dump-autoload
