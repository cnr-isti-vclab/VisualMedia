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

RUN mkdir -p /data/cache && chown www-data:www-data /data/cache && \
    mkdir -p /data/vms_upload && chown www-data:www-data /data/vms_upload && \
    mkdir -p /data/vms_data && chown www-data:www-data /data/vms_data


# on production TODO:
# - clone repo 
# - copy files from ./www to /var/www/html
