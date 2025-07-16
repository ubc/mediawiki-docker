FROM php:8.3-apache

ENV WIKI_VERSION_MAJOR_MINOR=1.43
ENV WIKI_VERSION_BUGFIX=2
ENV WIKI_VERSION=$WIKI_VERSION_MAJOR_MINOR.$WIKI_VERSION_BUGFIX
ENV WIKI_VERSION_STR=1_43

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmagickwand-dev \
        libicu-dev \
        libldap-dev \
        libldap-common \
        netcat-traditional \
        git \
        imagemagick \
        unzip \
        vim \
        libonig-dev \
        # for simpleSAMLphp
        libzip-dev \
        # for TimedMediaHandler
        ffmpeg \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/cache/apt/archives/* \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so \
    && docker-php-source extract

# install php extensions
# pcntl for Scribunto, zip for SimpleSAMLphp
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mbstring xml intl mysqli ldap pcntl opcache calendar zip imagick redis memcached

RUN a2enmod rewrite

WORKDIR /var/www/html

RUN curl -L https://releases.wikimedia.org/mediawiki/$WIKI_VERSION_MAJOR_MINOR/mediawiki-$WIKI_VERSION.tar.gz | tar xz --strip=1

COPY php.ini /usr/local/etc/php/

COPY mediawiki.conf /etc/apache2/
RUN echo "Include /etc/apache2/mediawiki.conf" >> /etc/apache2/apache2.conf \
    && rm /etc/apache2/sites-enabled/000-default.conf \
    && a2enmod proxy \
    && a2enmod proxy_http \
    && a2enmod remoteip

COPY docker-entrypoint.sh /entrypoint.sh
COPY docker-startuptasks.sh /startuptasks.sh
COPY LocalSettings.php /var/www/html/LocalSettings.php
COPY CustomHooks.php /var/www/html/CustomHooks.php
COPY composer.local.json /var/www/html/composer.local.json
COPY robots.txt /var/www/html/robots.txt
COPY resourcesCustom /var/www/html/resourcesCustom

# composer won't load plugins if we don't explicitly allow executing as root
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -L https://getcomposer.org/installer | php \
    && php composer.phar install --no-dev

RUN EXTS=`curl https://extdist.wmflabs.org/dist/extensions/ | awk 'BEGIN { FS = "\""  } ; {print $2}'` \
    && for i in SmiteSpam VisualEditor Scribunto LiquidThreads Cite WikiEditor LDAPProvider PluggableAuth LDAPAuthentication2 ParserFunctions TemplateData InputBox Widgets Variables RightFunctions CategoryTree LabeledSectionTransclusion UserPageEditProtection Quiz Collection DeleteBatch LinkTarget HitCounters Math 3D MultimediaViewer TimedMediaHandler SimpleSAMLphp; do \
      FILENAME=`echo "$EXTS" | grep ^${i}-REL${WIKI_VERSION_STR}`; \
      echo "Installing https://extdist.wmflabs.org/dist/extensions/$FILENAME"; \
      curl -Ls https://extdist.wmflabs.org/dist/extensions/$FILENAME | tar xz -C /var/www/html/extensions; \
    done \
    && echo "Installing https://github.com/ubc/EmbedPage/archive/v2.0.2.tar.gz" \
    && mkdir /var/www/html/extensions/EmbedPage \
    && curl -Ls https://github.com/ubc/EmbedPage/archive/v2.0.1.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/EmbedPage \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-UploadWizard/archive/refs/heads/REL1_43.tar.gz" \
    && mkdir /var/www/html/extensions/UploadWizard \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-UploadWizard/archive/refs/heads/REL1_43.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/UploadWizard \
    && echo "Installing https://github.com/smarty-php/smarty/archive/v3.1.44.tar.gz" \
    && mkdir -p /var/www/html/extensions/Widgets/smarty \
    && curl -Ls https://github.com/smarty-php/smarty/archive/v3.1.44.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/Widgets/smarty \
    && echo "Installing https://github.com/SkizNet/mediawiki-GTag/archive/master.tar.gz" \
    && mkdir -p /var/www/html/extensions/GTag \
    && curl -Ls https://github.com/SkizNet/mediawiki-GTag/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/GTag\
    && echo "Installing https://github.com/ubc/mediawiki-extensions-caliper/archive/v2.0.5.tar.gz" \
    && mkdir -p /var/www/html/extensions/caliper \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-caliper/archive/v2.0.5.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/caliper \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-ubcauth/archive/REL1_43.tar.gz" \
    && mkdir -p /var/www/html/extensions/UBCAuth\
    && curl -Ls https://github.com/ubc/mediawiki-extensions-ubcauth/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/UBCAuth \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-AutoCreatedUserRedirector/archive/master.tar.gz" \
    && mkdir -p /var/www/html/extensions/AutoCreatedUserRedirector \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-AutoCreatedUserRedirector/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/AutoCreatedUserRedirector \
    # WARNING: if updating DynamicPageList3 from 3.5.1, check if fix below is still required \
    && echo "Installing https://github.com/Universal-Omega/DynamicPageList3/archive/refs/tags/3.6.4.tar.gz" \
    && mkdir -p /var/www/html/extensions/DynamicPageList \
    && curl -Ls https://github.com/Universal-Omega/DynamicPageList3/archive/refs/tags/3.6.4.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/DynamicPageList
    ##Comment out to use with MW Extension method
    ##&& echo "Installing SmiteSpam https://github.com/wikimedia/mediawiki-extensions-SmiteSpam/archive/REL1_39.zip" \
    ##&& curl -L -o smitespam.zip https://github.com/wikimedia/mediawiki-extensions-SmiteSpam/archive/REL1_39.zip \
    ##&& unzip smitespam.zip -d /var/www/html/extensions/ \
    ##&& mv /var/www/html/extensions/mediawiki-extensions-SmiteSpam-REL1_39 /var/www/html/extensions/SmiteSpam
    #&& echo "Installing patched Math extension from https://github.com/ubc/mediawiki-extensions-Math/archive/REL1_35.tar.gz" \
    #&& mkdir -p /var/www/html/extensions/Math \
    #&& curl -Ls https://github.com/ubc/mediawiki-extensions-Math/archive/REL1_35.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/Math

# composer.local.json merges in composer.json from caliper extension, so we
# need to run composer update after getting the extensions.
RUN php composer.phar update --no-dev

RUN mkdir -p /data \
   && chmod a+rw /var/www/html/extensions/Widgets/compiled_templates

VOLUME /data

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]
