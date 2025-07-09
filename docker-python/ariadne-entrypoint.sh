#!/bin/sh

if [ ! -d "/data/vms_upload" ]; then
  mkdir -p /data/vms_upload
fi

if [ ! -d "/data/vms_data" ]; then
  mkdir -p /data/vms_data
fi

. ariadne-venv/bin/activate
#python3 /app/ariadne.py

#this is used for development
python3 /ariadne/ariadne.py

