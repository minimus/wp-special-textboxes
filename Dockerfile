FROM wordpress:5.8.1-php8.0-apache

#RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY ./tools/php.ini "$PHP_INI_DIR/conf.d"
COPY ./tools/wp-config.php /var/www/html
# COPY ./*.php /var/www/html/wp-content/plugins/wp-special-textboxes
# COPY ./css /var/www/html/wp-content/plugins/wp-special-textboxes
# COPY ./js /var/www/html/wp-content/plugins/wp-special-textboxes