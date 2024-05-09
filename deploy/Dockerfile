ARG Docker_images

FROM ${Docker_images}

WORKDIR /var/www/html/

COPY --chown=www-data:www-data . .

EXPOSE 9000
