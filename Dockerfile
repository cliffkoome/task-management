FROM php:8.2-apache

# Disable conflicting MPM modules, enable prefork
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# Enable pdo_mysql and rewrite
RUN docker-php-ext-install pdo pdo_mysql
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Apache config to allow .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf \
&& a2enconf app

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]