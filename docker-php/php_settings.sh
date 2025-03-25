#!/bin/bash

echo "short_open_tag = On" >> $PHP_INI_DIR/php.ini
echo "post_max_size = 4000M" >> $PHP_INI_DIR/php.ini
echo "upload_max_filesize = 4000M" >> $PHP_INI_DIR/php.ini
echo "max_execution_time = 120" >> $PHP_INI_DIR/php.ini

echo $ADMIN_EMAIL >> /var/www/html/.env
echo $SMTP_USER >> /var/www/html/.env
echo $SMTP_PASSWD >> /var/www/html/.env
echo $SMTP_HOST >> /var/www/html/.env
echo $SMTP_PORT >> /var/www/html/.env
