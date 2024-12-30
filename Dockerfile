# Multi-stage Dockerfile for the Attendize application layer images

FROM wyveo/nginx-php-fpm:php74 as base

# Add GPG keys for the nginx and sury PHP repositories to fix signature issues
RUN apt-get update && apt-get install -y curl gnupg2 \
    && install -m 0755 -d /usr/share/keyrings \
    \
    # Update Nginx GPG key
    && curl -fsSL https://nginx.org/keys/nginx_signing.key | gpg --dearmor -o /usr/share/keyrings/nginx-archive-keyring.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/nginx-archive-keyring.gpg] http://nginx.org/packages/mainline/debian/ buster nginx" > /etc/apt/sources.list.d/nginx.list \
    \
    # Update Sury PHP GPG key
    && curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /usr/share/keyrings/sury-php.gpg \
    && echo "deb [signed-by=/usr/share/keyrings/sury-php.gpg] https://packages.sury.org/php/ buster main" > /etc/apt/sources.list.d/php.list \
    \
    && apt-get update || true \
    && apt-get install -y wait-for-it libxrender1 || true

# Set up code
WORKDIR /usr/share/nginx/html
COPY . .

# Ensure the setup script is executable
RUN chmod +x scripts/setup

# Run composer, chmod files, setup Laravel key
RUN ./scripts/setup

# The worker container runs the Laravel queue in the background
FROM base as worker

CMD ["php", "artisan", "queue:work", "--daemon"]

# The web container runs the HTTP server and connects to all other services in the application stack
FROM base as web

# Nginx config
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Self-signed SSL certificate for HTTPS support
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/nginx-selfsigned.key \
    -out /etc/ssl/certs/nginx-selfsigned.crt \
    -subj "/C=GB/ST=London/L=London/O=NA/CN=localhost" \
    && openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048 \
    && mkdir /etc/nginx/snippets

COPY self-signed.conf /etc/nginx/snippets/self-signed.conf
COPY ssl-params.conf /etc/nginx/snippets/ssl-params.conf

# Ports to expose
EXPOSE 80
EXPOSE 443

# Starting Nginx server
CMD ["/bin/sh", "/start.sh"]