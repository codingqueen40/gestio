FROM php:8.4-apache

# PHP extensions required for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && docker-php-ext-enable mysqli

# Enable mod_rewrite (clean URLs) and mod_headers (security headers, ticket #23)
RUN a2enmod rewrite headers

# Apache serves the public/ subfolder only.
# Application code (config, templates, classes) lives in /var/www/html/src,
# OUTSIDE the document root, so it is never reachable via a URL.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Default PHP settings = PRODUCTION-SAFE.
# display_errors stays OFF: no error (nor SQL message) is ever shown to the visitor.
# Errors are only logged. Dev re-enables display via php/dev.ini (mounted by the override).
RUN { \
      echo "display_errors = Off"; \
      echo "log_errors = On"; \
      echo "error_reporting = E_ALL"; \
      echo "date.timezone = Europe/Berlin"; \
      echo "expose_php = Off"; \
    } > /usr/local/etc/php/conf.d/zz-app.ini

# Security headers (ticket #23), portés par Apache — voir php/security-headers.conf.
# Nommé "zz-" pour se charger APRÈS le security.conf de Debian (inclusion alphabétique),
# sinon son "ServerTokens OS / ServerSignature On" écraserait nos "Prod / Off".
COPY php/security-headers.conf /etc/apache2/conf-available/zz-security-headers.conf
RUN a2enconf zz-security-headers

WORKDIR /var/www/html

# msmtp (relay SMTP) + poppler-utils (pdftotext pour l'import PDF).
RUN apt-get update && apt-get install -y --no-install-recommends msmtp msmtp-mta poppler-utils \
    && rm -rf /var/lib/apt/lists/*

COPY php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

CMD ["/usr/local/bin/entrypoint.sh"]
