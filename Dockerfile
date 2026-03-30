FROM php:8.2-apache

# Enable pdo_mysql and mod_rewrite
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Apache config
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf \
&& a2enconf app

# Set working directory
WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]