FROM ubuntu:24.04

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 \
    php8.3 \
    php8.3-pgsql \
    php8.3-curl \
    python3 \
    python3-pip \
    sudo && \
    # add ubuntu user to sudoers
    echo ubuntu ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/ubuntu && \
    chmod 0440 /etc/sudoers.d/ubuntu && \
    mkdir -p /scripts

COPY scripts/php_settings.sh /scripts
RUN chmod +x scripts/php_settings.sh && \
    ./scripts/php_settings.sh && \
    rm -rf /scripts && \
    service apache2 restart

RUN python3 -m pip install --upgrade pip && \
    python3 -m pip install psycopg2-binary daemon requests --break-system-packages

# clean
RUN apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

# todo:
#    - check for other required dependencies
#    - edit /etc/php/8.3/apache2/php.ini
#    - install python dependencies
#    - copy all required files to /var/www/html?
