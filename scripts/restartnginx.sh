#!/bin/bash
folder=health2.0-backstage-pre

setfacl -R -m u:nginx:rwx,u:ec2-user:rwx /var/www/html/$folder
setfacl -dR -m u:nginx:rwx,u:ec2-user:rwx /var/www/html/$folder

if [ -d /tmp/$folder ]; then
    if [ -e /tmp/$folder/.env ]; then
        cp /tmp/$folder/.env /var/www/html/$folder
    fi

    if [ -d /var/www/html/$folder/vendor ]; then
        rsync -avh /tmp/$folder/vendor /var/www/html/$folder
    else
        cp -pR /tmp/$folder/vendor /var/www/html/$folder/
    fi

fi

setfacl -R -m u:nginx:r,u:ec2-user:rwx /var/www/html/$folder/vendor
setfacl -dR -m u:nginx:r,u:ec2-user:rwx /var/www/html/$folder/vendor
setfacl -R -m u:nginx:rwx,u:ec2-user:rwx /var/www/html/$folder/public
setfacl -dR -m u:nginx:rwx,u:ec2-user:rwx /var/www/html/$folder/public

# AWS Parameter store
REGION="ap-northeast-1"
PARAMETER="tvbs-package-token"

cd /var/www/html/$folder && \
composer dump-autoload && \
composer config --global github-oauth.github.com `aws ssm get-parameter --with-decryption --name $PARAMETER --region $REGION | jq -r '.Parameter.Value'` && \
composer install && \
php artisan storage:link && \
php artisan migrate && \
php artisan EditPermissionForUser:Run

if [ -d /tmp/$folder/pma ]; then
    cp -pR /tmp/$folder/pma /var/www/html/$folder/public/
fi

mkdir /var/www/html/$folder/public/images

chown -R nginx:nginx /var/www/html/$folder

systemctl restart nginx

rm -rf /tmp/$folder
