# Используем официальный PHP с Apache
FROM php:8.2-apache

# Устанавливаем расширения
RUN docker-php-ext-install pdo pdo_mysql

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем рабочую директорию
WORKDIR /var/www/html

# Копируем файлы проекта
COPY . .

# Устанавливаем зависимости
RUN composer install --no-dev --optimize-autoloader

# Настраиваем Apache: указываем корень на папку public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Разрешаем .htaccess, если ты его будешь использовать
RUN a2enmod rewrite

# Открываем порт 80
EXPOSE 80

# Стартуем Apache в foreground
CMD ["apache2-foreground"]
