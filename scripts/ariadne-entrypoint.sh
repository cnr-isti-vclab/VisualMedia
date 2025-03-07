#!/bin/sh

if [ ! -d "/data/vms_upload" ]; then
  mkdir -p /data/vms_upload
fi

if [ ! -d "/data/vms_data" ]; then
  mkdir -p /data/vms_data
fi

echo "Starting Ariadne"
service apache2 start
. /ariadne-venv/bin/activate
python /scripts/ariadne.py

