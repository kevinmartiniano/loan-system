FROM webdevops/php-nginx-dev:7.4

ENV WEB_DOCUMENT_ROOT=/var/www/html/public

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .

RUN composer install

RUN usermod -a -G www-data application
RUN find . -type f -exec chmod 664 {} \;
RUN find . -type d -exec chmod 775 {} \;
