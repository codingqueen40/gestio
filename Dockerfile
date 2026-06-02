FROM php:8.4-apache

# PHP extensions required for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli

# Enable mod_rewrite (useful for clean URLs later)
RUN a2enmod rewrite

# Default PHP settings = PRODUCTION-SAFE.
# display_errors stays OFF: no error (nor SQL message) is ever shown to the visitor.
# Errors are only logged. Dev re-enables display via php/dev.ini (mounted by the override).
RUN { \
      echo "display_errors = Off"; \
      echo "log_errors = On"; \
      echo "error_reporting = E_ALL"; \
      echo "date.timezone = Europe/Berlin"; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www/html
