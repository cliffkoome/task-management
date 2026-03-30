FROM php:8.2-apache

# Install pdo_mysql
RUN docker-php-ext-install pdo pdo_mysql

# Fix MPM conflict - disable event, enable prefork
RUN sed -i 's/^#\(.*mpm_prefork\)/\1/' /etc/apache2/mods-enabled/*.load 2>/dev/null || true
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf
RUN ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load
RUN ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

# Enable rewrite
RUN a2enmod rewrite

# Copy files
COPY . /var/www/html/

# Allow .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf \
&& a2enconf app

WORKDIR /var/www/html
EXPOSE 80
CMD ["apache2-foreground"]