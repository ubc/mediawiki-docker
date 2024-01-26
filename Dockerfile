FROM php:7.4-apache

ENV WIKI_VERSION_MAJOR_MINOR=1.39
ENV WIKI_VERSION_BUGFIX=6
ENV WIKI_VERSION=$WIKI_VERSION_MAJOR_MINOR.$WIKI_VERSION_BUGFIX
ENV WIKI_VERSION_STR=1_39

RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libmagickwand-dev \
        libicu-dev \
        libldap2-dev \
        libldap-2.4-2 \
        libldap-common \
        netcat \
        git \
        imagemagick \
        unzip \
        vim.tiny \
        libonig-dev \
        # for TimedMediaHandler
        ffmpeg \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/cache/apt/archives/* \
    && ln -s /usr/lib/x86_64-linux-gnu/libldap.so /usr/lib/libldap.so \
    && ln -s /usr/lib/x86_64-linux-gnu/liblber.so /usr/lib/liblber.so \
    && docker-php-source extract

# pcntl for Scribunto
RUN docker-php-ext-install -j$(nproc) mbstring xml intl mysqli ldap pcntl opcache calendar \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-source delete \
    && pecl install imagick-3.4.3 \
    && pecl install redis \
    && docker-php-ext-enable imagick mysqli redis \
    && a2enmod rewrite \
    && rm -rf /tmp/pear

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

# composer won't load plugins if we don't explicitly allow executing as root
ENV COMPOSER_ALLOW_SUPERUSER=1
# FIXME temp hack to use lastest composer 1.x. composer 2.x version will break wikimedia/composer-merge-plugin
RUN curl -L https://getcomposer.org/installer | php \
#RUN curl -L https://getcomposer.org/composer-1.phar --output composer.phar \
    && php composer.phar install --no-dev

RUN EXTS=`curl https://extdist.wmflabs.org/dist/extensions/ | awk 'BEGIN { FS = "\""  } ; {print $2}'` \
    && for i in SmiteSpam VisualEditor Scribunto LiquidThreads Cite WikiEditor LDAPProvider PluggableAuth LDAPAuthentication2 ParserFunctions TemplateData InputBox Widgets Variables RightFunctions PageInCat CategoryTree LabeledSectionTransclusion UserPageEditProtection Quiz Collection DeleteBatch LinkTarget HitCounters Math 3D MultimediaViewer TimedMediaHandler; do \
      FILENAME=`echo "$EXTS" | grep ^${i}-REL${WIKI_VERSION_STR}`; \
      echo "Installing https://extdist.wmflabs.org/dist/extensions/$FILENAME"; \
      curl -Ls https://extdist.wmflabs.org/dist/extensions/$FILENAME | tar xz -C /var/www/html/extensions; \
    done \
    && echo "Installing https://github.com/ubc/EmbedPage/archive/v2.0.2.tar.gz" \
    && mkdir /var/www/html/extensions/EmbedPage \
    && curl -Ls https://github.com/ubc/EmbedPage/archive/v2.0.1.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/EmbedPage \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-UploadWizard/archive/mw1.39.tar.gz" \
    && mkdir /var/www/html/extensions/UploadWizard \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-UploadWizard/archive/mw1.39.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/UploadWizard \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-UWUBCMessages/archive/master.tar.gz" \
    && mkdir /var/www/html/extensions/UWUBCMessages \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-UWUBCMessages/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/UWUBCMessages \
    && echo "Installing https://github.com/smarty-php/smarty/archive/v3.1.44.tar.gz" \
    && mkdir -p /var/www/html/extensions/Widgets/smarty \
    && curl -Ls https://github.com/smarty-php/smarty/archive/v3.1.44.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/Widgets/smarty \
    && echo "Installing https://github.com/SkizNet/mediawiki-GTag/archive/master.tar.gz" \
    && mkdir -p /var/www/html/extensions/GTag \
    && curl -Ls https://github.com/SkizNet/mediawiki-GTag/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/GTag\
    && echo "Installing https://github.com/ubc/mediawiki-extensions-caliper/archive/v2.0.5.tar.gz" \
    && mkdir -p /var/www/html/extensions/caliper \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-caliper/archive/v2.0.5.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/caliper \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-ubcauth/archive/master.tar.gz" \
    && mkdir -p /var/www/html/extensions/UBCAuth\
    && curl -Ls https://github.com/ubc/mediawiki-extensions-ubcauth/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/UBCAuth \
    && echo "Installing https://github.com/ubc/mediawiki-extensions-AutoCreatedUserRedirector/archive/master.tar.gz" \
    && mkdir -p /var/www/html/extensions/AutoCreatedUserRedirector \
    && curl -Ls https://github.com/ubc/mediawiki-extensions-AutoCreatedUserRedirector/archive/master.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/AutoCreatedUserRedirector \
    # WARNING: if updating DynamicPageList3 from 3.5.1, check if fix below is still required \
    && echo "Installing https://github.com/Universal-Omega/DynamicPageList3/archive/refs/tags/3.5.1.tar.gz" \
    && mkdir -p /var/www/html/extensions/DynamicPageList \
    && curl -Ls https://github.com/Universal-Omega/DynamicPageList3/archive/refs/tags/3.5.1.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/DynamicPageList
    ##Comment out to use with MW Extension method
    ##&& echo "Installing SmiteSpam https://github.com/wikimedia/mediawiki-extensions-SmiteSpam/archive/REL1_39.zip" \
    ##&& curl -L -o smitespam.zip https://github.com/wikimedia/mediawiki-extensions-SmiteSpam/archive/REL1_39.zip \
    ##&& unzip smitespam.zip -d /var/www/html/extensions/ \
    ##&& mv /var/www/html/extensions/mediawiki-extensions-SmiteSpam-REL1_39 /var/www/html/extensions/SmiteSpam
    #&& echo "Installing patched Math extension from https://github.com/ubc/mediawiki-extensions-Math/archive/REL1_35.tar.gz" \
    #&& mkdir -p /var/www/html/extensions/Math \
    #&& curl -Ls https://github.com/ubc/mediawiki-extensions-Math/archive/REL1_35.tar.gz | tar xz --strip=1 -C /var/www/html/extensions/Math

# WARNING: Below fix is only for DynamicPageList3 3.5.1
# Patch to fix Math Exam Resources DPL
COPY ./extensions/DynamicPageList/includes/Query.php /var/www/html/extensions/DynamicPageList/includes/Query.php
# TODO: Remove if >REL1_40, as this is a backport from Vector REL1_40
# Add login button next to "..." menu in top-right corner
COPY skins/Vector/includes/Hooks.php /var/www/html/skins/Vector/includes/Hooks.php
COPY skins/Vector/includes/SkinVector.php /var/www/html/skins/Vector/includes/SkinVector.php
# TODO: Also remove on upgrade, this is a Vector customization to make the main
# menu behave more like current Wikipedia (dropdown over the page)
COPY skins/Vector/resources/skins.vector.styles/components/Sidebar.less \
     /var/www/html/skins/Vector/resources/skins.vector.styles/components/Sidebar.less
COPY skins/Vector/resources/skins.vector.styles/components/TableOfContents.less \
     /var/www/html/skins/Vector/resources/skins.vector.styles/components/TableOfContents.less
COPY skins/Vector/resources/skins.vector.styles/layouts/screen.less \
     /var/www/html/skins/Vector/resources/skins.vector.styles/layouts/screen.less
COPY skins/Vector/includes/templates/Sidebar.mustache \
     /var/www/html/skins/Vector/includes/templates/Sidebar.mustache
COPY skins/Vector/includes/templates/skin.mustache \
     /var/www/html/skins/Vector/includes/templates/skin.mustache
COPY skins/Vector/resources/skins.vector.styles/components/MenuTabs.less \
     /var/www/html/skins/Vector/resources/skins.vector.styles/components/MenuTabs.less

# composer.local.json merges in composer.json from caliper extension, so we
# need to run composer update after getting the extensions.
RUN php composer.phar update --no-dev

# TODO: Once we move past 1.39, test if patch is still necessary, create a page
# with source:
# [[File:Hunga Tonga–Hunga Ha'apai volcanic eruption captured at December 30, 2021.webm|thumb|test]]
# See if VisualEditor throws an when you tried to edit the page.
# Patch from: https://phabricator.wikimedia.org/T350594
COPY AddMediaInfo.patch ./AddMediaInfo.patch
RUN git apply AddMediaInfo.patch

RUN mkdir -p /data \
   && chmod a+rw /var/www/html/extensions/Widgets/compiled_templates

VOLUME /data

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apachectl", "-e", "info", "-D", "FOREGROUND"]
