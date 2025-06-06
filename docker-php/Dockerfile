FROM --platform=linux/amd64 php:8.3-apache

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    a2enmod rewrite && \
    docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql && \
    docker-php-ext-install pgsql pdo_pgsql
    
COPY ./php_settings.sh /

RUN chmod +x /php_settings.sh && \
    /php_settings.sh && \
    rm -rf /php_settings.sh 

# on production TODO:
# - clone repo 
# - copy files from ./www to /var/www/html
