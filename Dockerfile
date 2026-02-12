FROM dunglas/frankenphp:latest

WORKDIR /app

RUN install-php-extensions \
    pdo_pgsql \
    apcu \
    gd \
    intl \
    opcache \
    xdebug \
    zip

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . .
RUN chmod +x bin/console

# Copy certificates for HTTPS
COPY localhost.crt /app/certs/
COPY localhost.key /app/certs/

# Set environment variables for FrankenPHP and Symfony Runtime
ENV FRANKENPHP_CONFIG="worker /app/public/index.php"
ENV APP_RUNTIME="Runtime\\FrankenPhpSymfony\\Runtime"
ENV SERVER_NAME=localhost

EXPOSE 80 443
