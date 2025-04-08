FROM ubuntu:24.04

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
        libglx0 \
        libegl1 \
        libgl1 \
        libopengl0 \
        python3 \
        python3-pip \
        python3-venv \
        sudo \
        wget && \
    # add ubuntu user to sudoers
    echo ubuntu ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/ubuntu && \
    chmod 0440 /etc/sudoers.d/ubuntu && \
    mkdir -p /scripts

COPY ./ariadne-entrypoint.sh /

RUN chmod +x /ariadne-entrypoint.sh

COPY ./ariadne.py /
RUN python3 -m venv ariadne-venv && \
    . ariadne-venv/bin/activate && \
    python -m pip install --upgrade pip && \
    python -m pip install psycopg2-binary daemon requests

# download relight
RUN cd /home/ubuntu && \
    mkdir -p relight && \
    cd relight && \
    wget https://github.com/cnr-isti-vclab/relight/releases/download/RelightLab-2024.11/RelightLab2024.11-linux.tar.gz && \
    tar -xzf RelightLab2024.11-linux.tar.gz && \
    rm RelightLab2024.11-linux.tar.gz

# clean
RUN apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/ariadne-entrypoint.sh"]

