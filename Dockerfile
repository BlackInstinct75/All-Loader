FROM php:8.2-apache

# Instalar dependencias del sistema, ffmpeg y python3-pip
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Instalar Deno globalmente en /usr/local/bin (runtime JS nativo para yt-dlp)
RUN curl -fsSL https://deno.land/install.sh | DENO_INSTALL=/usr/local sh

# Instalar yt-dlp completo con el solver EJS incluido
RUN pip3 install --break-system-packages -U "yt-dlp[default]"

# Habilitar mod_rewrite y apuntar Apache a /public
RUN a2enmod rewrite \
    && sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html/

RUN mkdir -p /var/www/html/storage/downloads \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

EXPOSE 80