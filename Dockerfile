FROM php:8.2-apache

# Extensiones de PHP que necesita GeoPOS (CodeIgniter 3 + MySQL)
RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        mysqli \
        pdo_mysql \
        gd \
        intl \
        zip \
        bcmath \
        exif \
        mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# OpenSSL 3: activar el provider "legacy" para poder abrir certificados .p12 con
# cifrado RC2/3DES (frecuente en los emitidos por prestadores de Paraguay para SIFEN)
RUN sed -i \
        -e 's/^default = default_sect$/default = default_sect\nlegacy = legacy_sect/' \
        -e 's/^# activate = 1$/activate = 1/' \
        /etc/ssl/openssl.cnf \
    && printf '\n[legacy_sect]\nactivate = 1\n' >> /etc/ssl/openssl.cnf

# Apache: mod_rewrite, mod_headers, mod_ssl y certificado base
RUN a2enmod rewrite headers ssl socache_shmcb \
    && openssl req -x509 -nodes -days 3650 -newkey rsa:2048 \
       -keyout /etc/ssl/private/ssl-cert-snakeoil.key \
       -out /etc/ssl/certs/ssl-cert-snakeoil.pem \
       -subj "/C=PY/ST=Central/L=Asuncion/O=GeeksPOS/CN=localhost" \
    && a2ensite default-ssl

# Permitir que el .htaccess del proyecto funcione
RUN sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

# Zona horaria y subida de archivos (ajusta a tu gusto)
RUN { \
        echo 'upload_max_filesize=64M'; \
        echo 'post_max_size=64M'; \
        echo 'memory_limit=256M'; \
        echo 'max_execution_time=120'; \
        echo 'date.timezone=America/Asuncion'; \
    } > /usr/local/etc/php/conf.d/geekspos.ini

WORKDIR /var/www/html

# Copiamos el código (en desarrollo lo sobreescribe el bind mount del compose)
COPY . /var/www/html

# Carpetas que la app necesita escribir
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 application/cache application/logs userfiles 2>/dev/null || true

EXPOSE 80 443
