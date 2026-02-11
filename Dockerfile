FROM dunglas/frankenphp:latest

WORKDIR /app

RUN install-php-extensions \
    pdo_mysql \
    apcu \
    gd \
    intl \
    opcache \
    zip

# Copier Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier l'application
COPY . .
RUN chmod +x bin/console

# Copier les certificats SSL
COPY localhost.crt /app/certs/
COPY localhost.key /app/certs/

# FrankenPHP avec Symfony
ENV FRANKENPHP_CONFIG="worker /app/public/index.php"
ENV APP_RUNTIME="Runtime\\FrankenPhpSymfony\\Runtime"
ENV SERVER_NAME=localhost

EXPOSE 80 443
