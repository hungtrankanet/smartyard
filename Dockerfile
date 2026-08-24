FROM php:8.1-apache

ENV DEBIAN_FRONTEND=noninteractive

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd mysqli pdo pdo_mysql zip intl exif

# Enable Apache modules
RUN a2enmod rewrite headers

# Apache config: allow .htaccess overrides, set ServerName, relaxed protocol options for Docker proxy
RUN echo 'ServerName localhost\n\
HttpProtocolOptions Unsafe\n\
LimitRequestLine 65536\n\
LimitRequestFieldSize 65536\n\
\n\
<VirtualHost *:80>\n\
    ServerName localhost\n\
    ServerAlias *\n\
    DocumentRoot /var/www/html\n\
    DirectoryIndex index.php index.html\n\
    AcceptPathInfo On\n\
    AllowEncodedSlashes On\n\
    <Directory /var/www/html>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes +FollowSymLinks\n\
    </Directory>\n\
    # Pass Authorization header for API\n\
    SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "HttpProtocolOptions Unsafe" >> /etc/apache2/apache2.conf

# PHP config for production & development
RUN echo "upload_max_filesize = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/uploads.ini

# Set working directory
WORKDIR /var/www/html

# Ensure writable and uploads directories exist with proper permissions
RUN mkdir -p /var/www/html/writable/cache \
             /var/www/html/writable/logs \
             /var/www/html/writable/session \
             /var/www/html/writable/uploads \
             /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/writable /var/www/html/uploads \
    && chmod -R 777 /var/www/html/writable /var/www/html/uploads

# Expose port 80
EXPOSE 80
