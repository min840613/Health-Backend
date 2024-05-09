#!/bin/bash
folder=health2.0-backstage-pre

source /home/ec2-user/.bashrc

cd /var/www/html/$folder && \
npm install && \
npm run build
