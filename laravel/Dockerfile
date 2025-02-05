# Use PHP com Apache como imagem base
FROM php:8.2-apache as web

# Instala dependências do sistema e o driver MySQL
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    mariadb-client \
    && docker-php-ext-install zip \
    && docker-php-ext-install fileinfo \
    && docker-php-ext-install pdo_mysql

# Limpa cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Configura o DocumentRoot para apontar para o diretório public do Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Instala o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia os arquivos do projeto (incluindo composer.json e composer.lock)
COPY . /var/www/html

# Define o diretório de trabalho
WORKDIR /var/www/html

# Ajusta permissões antes da instalação
RUN chown -R www-data:www-data /var/www/html

# Instala as dependências do Composer
RUN composer install

# Ajusta permissões finais
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
