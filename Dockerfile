FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Install Apache, PHP and extensions
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysql \
    php-pdo \
    libapache2-mod-php \
    && rm -rf /var/lib/apt/lists/*

# Enable rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Apache config
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/app.conf \
&& a2enconf app

# Set port
RUN sed -i 's/Listen 80/Listen ${PORT:-80}/' /etc/apache2/ports.conf
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost *:${PORT:-80}>/' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]