#!/bin/bash
set -e

cd /home/ubuntu/Lamp-Stack

sudo rsync -av --delete . /var/www/html/

echo "Deployed to /var/www/html"

