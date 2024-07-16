FROM php:7.4-apache
WORKDIR /var/www

# Install composer & php extension installer
COPY --from=composer/composer:2-bin /composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && \
    apt-get -y install git curl vim  && \
    install-php-extensions zip memcached

# create dirs we need and make sure apache user can access them
RUN mkdir simplesamlphp-base/ /var/cache/simplesamlphp simplesamlphp/ && \
    chown www-data. simplesamlphp-base/ simplesamlphp/ /var/cache/simplesamlphp
# Composer doesn't like to be root, so we'll run the rest as the apache user
USER www-data

# Install simplesamlphp, note we're using an older version for compatibility
# as the wiki simplesamlphp extension is in a php7 environment
ARG SIMPLESAMLPHP_TAG=v2.0.13
RUN git clone --branch $SIMPLESAMLPHP_TAG https://github.com/simplesamlphp/simplesamlphp.git simplesamlphp-base
WORKDIR /var/www/simplesamlphp-base

# delete the cert directory as it'll conflict with the one we'll mount into the
# container
RUN rm -r cert/

# Use composer to install dependencies
RUN composer install && \
    composer require simplesamlphp/simplesamlphp-module-metarefresh

# Copy config files
COPY config/ config/

COPY apache.conf /etc/apache2/sites-available/000-default.conf

# copy our custom entrypoint
COPY custom-entrypoint /usr/local/bin/

# The wiki extension expects the simplesamlphp SP to be installed on the same
# server and will import modules from the running SP. But we want the SP to run
# in its own container. The only way the wiki container will be able to access
# the SP code is if it's a shared volume. So we have to run the SP from a
# shared volume. This custom entrypoint copies the SP code with all the
# customizations and stuff into the shared volume presumably mounted at
# /var/www/simplesamlphp.
WORKDIR /var/www/simplesamlphp
ENTRYPOINT ["custom-entrypoint"]
CMD ["apache2-foreground"]
