#!/bin/sh

# if /data/vms_upload directory does not exist, create it
if [ ! -d "/data/vms_upload" ]; then
  mkdir /data/vms_upload
fi

# if /data/vms_data directory does not exist, create it
if [ ! -d "/data/vms_data" ]; then
  mkdir /data/vms_data
fi

echo "Starting Ariadne"
service apache2 start
. /ariadne-venv/bin/activate
python /scripts/ariadne.py

