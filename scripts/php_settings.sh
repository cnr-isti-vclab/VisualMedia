#!/bin/bash

echo "short_open_tag = On" >> /etc/php/8.3/apache2/php.ini
echo "post_max_size = 4000M" >> /etc/php/8.3/apache2/php.ini
echo "upload_max_filesize = 4000M" >> /etc/php/8.3/apache2/php.ini
echo "max_execution_time = 120" >> /etc/php/8.3/apache2/php.ini