FROM ubuntu:24.04

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
        apache2 \
        php8.3 \
        php8.3-pgsql \
        php8.3-curl \
        python3 \
        python3-pip \
        python3-venv \
        sudo && \
    # add ubuntu user to sudoers
    echo ubuntu ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/ubuntu && \
    chmod 0440 /etc/sudoers.d/ubuntu && \
    mkdir -p /scripts

COPY scripts/php_settings.sh /scripts
COPY scripts/ariadne.py /scripts
COPY scripts/ariadne-entrypoint.sh /

RUN chmod +x scripts/php_settings.sh && \
    chmod +x /ariadne-entrypoint.sh && \
    ./scripts/php_settings.sh && \
    rm -rf /scripts/php_settings.sh && \
    service apache2 restart

RUN python3 -m venv ariadne-venv && \
    . ariadne-venv/bin/activate && \
    python -m pip install --upgrade pip && \
    python -m pip install psycopg2-binary daemon requests

# clean
RUN apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/ariadne-entrypoint.sh"]

