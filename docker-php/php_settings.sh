#!/bin/bash

echo "short_open_tag = On" >> $PHP_INI_DIR/php.ini
echo "post_max_size = 4000M" >> $PHP_INI_DIR/php.ini
echo "upload_max_filesize = 4000M" >> $PHP_INI_DIR/php.ini
echo "max_execution_time = 120" >> $PHP_INI_DIR/php.ini
echo "log_errors = On" >> $PHP_INI_DIR/php.ini
echo "display_errors = On" >> $PHP_INI_DIR/php.ini

