FROM ubuntu:24.04

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
    php8.3 \
    php8.3-pgsql \
    php8.3-curl \
    python3 \
    python3-pip \
    sudo && \
    # add vms user to sudoers
    echo vms ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/vms && \
    chmod 0440 /etc/sudoers.d/vms

# clean
RUN apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

# todo:
#    - check for other required dependencies
#    - edit /etc/php/8.3/apache2/php.ini
#    - install python dependencies
#    - copy all required files to /var/www/html?

USER vms
