FROM php:8.3-apache

COPY ./php_settings.sh /

RUN chmod +x /php_settings.sh && \
    /php_settings.sh && \
    rm -rf /php_settings.sh 

