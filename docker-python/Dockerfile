FROM --platform=linux/amd64 ubuntu:24.04

RUN apt-get -y update && DEBIAN_FRONTEND=noninteractive apt-get -y install \
        libglx0 \
        libegl1 \
        libgl1 \
        libopengl0 \
        libvips-tools \
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

COPY ./ariadne.py /app
COPY ./processing.py /app

RUN python3 -m venv ariadne-venv && \
    . ariadne-venv/bin/activate && \
    python -m pip install --upgrade pip && \
    python -m pip install psycopg2-binary daemon requests pymeshlab

# set Relight and nexus versions
ENV RELIGHT_VERSION=2024.11
ENV NEXUS_VERSION=2025.05

# download relight
RUN cd /home/ubuntu && \
    mkdir -p relight && \
    cd relight && \
    wget https://github.com/cnr-isti-vclab/relight/releases/download/RelightLab-$RELIGHT_VERSION/RelightLab$RELIGHT_VERSION-linux.tar.gz && \
    tar -xzf RelightLab$RELIGHT_VERSION-linux.tar.gz && \
    rm RelightLab$RELIGHT_VERSION-linux.tar.gz && \
    wget https://github.com/cnr-isti-vclab/relight/archive/refs/tags/RelightLab-$RELIGHT_VERSION.tar.gz && \
    tar -xzf RelightLab-$RELIGHT_VERSION.tar.gz relight-RelightLab-$RELIGHT_VERSION/scripts/build_deepzoom.sh && \
    mv relight-RelightLab-$RELIGHT_VERSION/scripts/build_deepzoom.sh /home/ubuntu/relight && \
    rm -rf relight-RelightLab-$RELIGHT_VERSION && \
    rm -rf RelightLab-$RELIGHT_VERSION.tar.gz

# download nexus
RUN cd /home/ubuntu && \
    mkdir -p nexus && \
    cd nexus && \
    wget https://github.com/cnr-isti-vclab/nexus/releases/download/v$NEXUS_VERSION/nexus-linux-x86_64.tar.gz && \
    tar -xzf nexus-linux-x86_64.tar.gz && \
    rm nexus-linux-x86_64.tar.gz

# clean
RUN apt-get -y autoremove && \
    apt-get -y clean && \
    rm -rf /var/lib/apt/lists/*

ENTRYPOINT ["/ariadne-entrypoint.sh"]

