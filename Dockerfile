# Multi stage docker file for the Attendize application layer images

# Base image with nginx, php-fpm and composer built on debian
FROM wyveo/nginx-php-fpm:php74 as base
RUN apt-get update && apt-get install -y wait-for-it libxrender1

# Set up code
WORKDIR /usr/share/nginx/html
COPY . .

# run composer, chmod files, setup laravel key
RUN ./scripts/setup

# The worker container runs the laravel queue in the background
FROM base as worker

CMD ["php", "artisan", "queue:work", "--daemon"]

# The web container runs the HTTP server and connects to all other services in the application stack
FROM base as web

# nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# self-signed ssl certificate for https support
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/nginx-selfsigned.key -out /etc/ssl/certs/nginx-selfsigned.crt -subj "/C=GB/ST=London/L=London/O=NA/CN=localhost" \
    && openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048 \
    && mkdir /etc/nginx/snippets
COPY self-signed.conf /etc/nginx/snippets/self-signed.conf
COPY ssl-params.conf /etc/nginx/snippets/ssl-params.conf

# Ports to expose
EXPOSE 80
EXPOSE 443

# Starting nginx server
CMD ["/start.sh"]

# NOTE: if you are deploying to production with this image, you should extend this Dockerfile with another stage that
# performs clean up (i.e. removing composer) and installs your own dependencies (i.e. your own ssl certificate).
