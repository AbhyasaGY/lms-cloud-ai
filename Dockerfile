FROM php:8.3-fpm

RUN apt-get update && apt-get install -y 
git 
curl 
unzip 
zip 
libzip-dev 
libpng-dev 
libonig-dev 
libxml2-dev 
nodejs 
npm 
&& docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN npm install --legacy-peer-deps

RUN npm run build

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=8000
