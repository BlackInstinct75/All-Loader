# Usamos una imagen oficial de PHP con Apache (versión 8.2 o superior recomendada)
FROM php:8.2-apache

# Actualizar el sistema e instalar dependencias necesarias (ffmpeg, python3 para yt-dlp, curl, git, etc.)
RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    curl \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Descargar el binario oficial y actualizado de yt-dlp directamente al contenedor
RUN curl -L https://github.com/yt-dlp/yt-dlp/releases/latest/download/yt-dlp -o /usr/local/bin/yt-dlp \
    && chmod a+rx /usr/local/bin/yt-dlp

# Habilitar mod_rewrite de Apache si tu proyecto usa URLs amigables
RUN a2enmod rewrite

# Establecer el directorio de trabajo dentro del contenedor
WORKDIR /var/www/html

# Copiar los archivos del proyecto actual al directorio web de Apache
COPY . /var/www/html/

# Asegurar permisos correctos en la carpeta de almacenamiento de descargas
RUN mkdir -p /var/www/html/storage/downloads \
    && chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage

# Exponer el puerto 80 para la web
EXPOSE 80