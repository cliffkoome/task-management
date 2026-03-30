FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysql \
    libapache2-mod-php \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite

# Remove default page
RUN rm -rf /var/www/html/*

# Copy project files
COPY . /var/www/html/

# Apache config
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php index.html\n\
</Directory>\n\
ServerName localhost' > /etc/apache2/conf-available/app.conf \
&& a2enconf app

# Entrypoint
RUN printf '#!/bin/bash\n\
PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen $PORT/" /etc/apache2/ports.conf\n\
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/" /etc/apache2/sites-enabled/000-default.conf\n\
apache2ctl -D FOREGROUND\n' > /start.sh \
&& chmod +x /start.sh

EXPOSE 80
CMD ["/bin/bash", "/start.sh"]