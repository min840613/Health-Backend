#!/bin/bash
folder=health2.0-backstage-pre

if [ ! -d /tmp/$folder ]; then
    mkdir -p /tmp/$folder
fi

if [ -d /var/www/html/$folder/vendor ]; then
    if [ -d /tmp/$folder/vendor ]; then
        rsync -avh /var/www/html/$folder/vendor /tmp/$folder
    else
        cp -pR /var/www/html/$folder/vendor /tmp/$folder
    fi
fi

if [ -d /var/www/html/$folder/public/pma ]; then
    cp -pR /var/www/html/$folder/public/pma /tmp/$folder
fi

if [ -e /var/www/html/$folder/.env ]; then
    cp /var/www/html/$folder/.env /tmp/$folder/
fi

if [ -d /var/www/html/$folder ]; then
    rm -rf /var/www/html/$folder
fi
mkdir -vp /var/www/html/$folder
