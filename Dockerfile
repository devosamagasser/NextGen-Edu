FROM php:8.2-apache

# تحديث الحزم وتثبيت المتطلبات
RUN apt-get update && apt-get install -y \
    curl \
    g++ \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libpng-dev \
    libreadline-dev \
    sudo \
    unzip \
    zip \
    libzip-dev \  
 && rm -rf /var/lib/apt/lists/*

# إعدادات Apache
RUN echo "ServerName laravel-app.local" >> /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تفعيل mod_rewrite و mod_headers لـ Apache
RUN a2enmod rewrite headers

# تثبيت امتدادات PHP المطلوبة
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد Laravel
WORKDIR /var/www/html
COPY . .

# ضبط الصلاحيات
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
# فتح البورت 80 لـ Apache
EXPOSE 80

# تشغيل Apache عند بدء الحاوية
CMD ["apache2-foreground"]
