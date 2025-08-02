FROM php:8.2-apache

# Установим зависимости
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql

# Копируем файлы проекта
COPY . /var/www/html

# Меняем DocumentRoot на /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Включаем mod_rewrite (для .htaccess)
RUN a2enmod rewrite

# Настроим доступ к папке public через .htaccess
RUN echo '<Directory /var/www/html/public>\n\
    AllowOverride All\n\
</Directory>' >> /etc/apache2/apache2.conf

# Переходим в рабочую директорию
WORKDIR /var/www/html

# Устанавливаем зависимости PHP через Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Установим права (необязательно, но безопасно)
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html
