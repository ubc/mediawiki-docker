FROM php:5.6.33-apache

ENV WIKI_VERSION=1.30.0
ENV WIKI_VERSION_STR=1_30
ENV VECTOR_SKIN_VERSION=REL1_30-85a66bf

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        libmagickwand-dev \
        libicu-dev \
        libldap2-dev \
        libldap-2.4-2 \
        netcat \
        git \
        imagemagick \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/cache/apt/archives/* \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so \
    && docker-php-source extract

# pcntl for Scribunto
RUN docker-php-ext-install -j$(nproc) mysql mbstring xml intl mysqli ldap pcntl opcache \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-source delete \
    && pecl install imagick-3.4.3 \
    && docker-php-ext-enable imagick mysqli \
    && a2enmod rewrite \
    && rm -rf /tmp/pear

WORKDIR /var/www/html

RUN curl -L https://api.github.com/repos/wikimedia/mediawiki/tarball/$WIKI_VERSION | tar xz --strip=1

COPY php.ini /usr/local/etc/php/

COPY mediawiki.conf /etc/apache2/
RUN echo "Include /etc/apache2/mediawiki.conf" >> /etc/apache2/apache2.conf \
    && rm /etc/apache2/sites-enabled/000-default.conf \
    && a2enmod proxy \
    && a2enmod proxy_http

COPY docker-entrypoint.sh /entrypoint.sh
COPY LocalSettings.php /var/www/html/LocalSettings.php
COPY composer.local.json /var/www/html/composer.local.json

RUN curl -L https://getcomposer.org/installer | php \
    && php composer.phar install --no-dev

RUN curl -L https://extdist.wmflabs.org/dist/skins/Vector-${VECTOR_SKIN_VERSION}.tar.gz | tar xz -C /var/www/html/skins \
    && EXTS=`curl https://extdist.wmflabs.org/dist/extensions/ | awk 'BEGIN { FS = "\""  } ; {print $2}'` \
    && for i in VisualEditor Scribunto LiquidThreads Cite WikiEditor LdapAuthentication ParserFunctions TemplateData InputBox Widgets Math Variables RightFunctions PageInCat CategoryTree LabeledSectionTransclusion UserPageEditProtection Quiz UploadWizard Collection intersection; do \
      FILENAME=`echo "$EXTS" | grep ^${i}-REL${WIKI_VERSION_STR}`; \
      echo "Installing https://extdist.wmflabs.org/dist/extensions/$FILENAME"; \
      curl -Ls https://extdist.wmflabs.org/dist/extensions/$FILENAME | tar xz -C /var/www/html/extensions; \
    done \
    && mkdir /var/www/html/extensions/RatingBar \
    && curl -Ls https://github.com/redekopmark/RatingBar/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/RatingBar \
    && mkdir -p /var/www/html/extensions/Widgets/smarty \
    && curl -Ls https://github.com/smarty-php/smarty/archive/v3.1.30.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/Widgets/smarty

RUN mkdir -p /data \
   && chmod a+x /var/www/html/extensions/Scribunto/engines/LuaStandalone/binaries/lua5_1_5_linux_64_generic/lua \
   && chmod a+rw /var/www/html/extensions/Widgets/compiled_templates

VOLUME /data

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]
