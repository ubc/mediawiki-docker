#!/bin/bash

set -e

: ${MEDIAWIKI_SITE_NAME:=MediaWiki}
: ${MEDIAWIKI_SITE_LANG:=en}
: ${MEDIAWIKI_ADMIN_USER:=admin}
: ${MEDIAWIKI_ADMIN_PASS:=admin12345}
: ${MEDIAWIKI_DB_TYPE:=mysql}
: ${MEDIAWIKI_ENABLE_SSL:=false}
: ${MEDIAWIKI_UPDATE:=false}

if [ -z "$MEDIAWIKI_DB_HOST" ]; then
	if [ -n "$MYSQL_PORT_3306_TCP_ADDR" ]; then
		MEDIAWIKI_DB_HOST=$MYSQL_PORT_3306_TCP_ADDR
	elif [ -n "$POSTGRES_PORT_5432_TCP_ADDR" ]; then
		MEDIAWIKI_DB_TYPE=postgres
		MEDIAWIKI_DB_HOST=$POSTGRES_PORT_5432_TCP_ADDR
	elif [ -n "$DB_PORT_3306_TCP_ADDR" ]; then
		MEDIAWIKI_DB_HOST=$DB_PORT_3306_TCP_ADDR
	elif [ -n "$DB_PORT_5432_TCP_ADDR" ]; then
		MEDIAWIKI_DB_TYPE=postgres
		MEDIAWIKI_DB_HOST=$DB_PORT_5432_TCP_ADDR
	else
		echo >&2 'error: missing MEDIAWIKI_DB_HOST environment variable'
		echo >&2 '	Did you forget to --link your database?'
		exit 1
	fi
fi

if [ -z "$RESTBASE_URL" ]; then
	export RESTBASE_URL=restbase-is-not-specified
fi

if [ -z "$MEDIAWIKI_DB_USER" ]; then
	if [ "$MEDIAWIKI_DB_TYPE" = "mysql" ]; then
		echo >&2 'info: missing MEDIAWIKI_DB_USER environment variable, defaulting to "root"'
		MEDIAWIKI_DB_USER=root
	elif [ "$MEDIAWIKI_DB_TYPE" = "postgres" ]; then
		echo >&2 'info: missing MEDIAWIKI_DB_USER environment variable, defaulting to "postgres"'
		MEDIAWIKI_DB_USER=postgres
	else
		echo >&2 'error: missing required MEDIAWIKI_DB_USER environment variable'
		exit 1
	fi
fi

if [ -z "$MEDIAWIKI_DB_PASSWORD" ]; then
	if [ -n "$MYSQL_ENV_MYSQL_ROOT_PASSWORD" ]; then
		MEDIAWIKI_DB_PASSWORD=$MYSQL_ENV_MYSQL_ROOT_PASSWORD
	elif [ -n "$POSTGRES_ENV_POSTGRES_PASSWORD" ]; then
		MEDIAWIKI_DB_PASSWORD=$POSTGRES_ENV_POSTGRES_PASSWORD
	elif [ -n "$DB_ENV_MYSQL_ROOT_PASSWORD" ]; then
		MEDIAWIKI_DB_PASSWORD=$DB_ENV_MYSQL_ROOT_PASSWORD
	elif [ -n "$DB_ENV_POSTGRES_PASSWORD" ]; then
		MEDIAWIKI_DB_PASSWORD=$DB_ENV_POSTGRES_PASSWORD
	else
		echo >&2 'error: missing required MEDIAWIKI_DB_PASSWORD environment variable'
		echo >&2 '	Did you forget to -e MEDIAWIKI_DB_PASSWORD=... ?'
		echo >&2
		echo >&2 '	(Also of interest might be MEDIAWIKI_DB_USER and MEDIAWIKI_DB_NAME)'
		exit 1
	fi
fi

: ${MEDIAWIKI_DB_NAME:=mediawiki}

if [ -z "$MEDIAWIKI_DB_PORT" ]; then
	if [ -n "$MYSQL_PORT_3306_TCP_PORT" ]; then
		MEDIAWIKI_DB_PORT=$MYSQL_PORT_3306_TCP_PORT
	elif [ -n "$POSTGRES_PORT_5432_TCP_PORT" ]; then
		MEDIAWIKI_DB_PORT=$POSTGRES_PORT_5432_TCP_PORT
	elif [ -n "$DB_PORT_3306_TCP_PORT" ]; then
		MEDIAWIKI_DB_PORT=$DB_PORT_3306_TCP_PORT
	elif [ -n "$DB_PORT_5432_TCP_PORT" ]; then
		MEDIAWIKI_DB_PORT=$DB_PORT_5432_TCP_PORT
	elif [ "$MEDIAWIKI_DB_TYPE" = "mysql" ]; then
		MEDIAWIKI_DB_PORT="3306"
	elif [ "$MEDIAWIKI_DB_TYPE" = "postgres" ]; then
		MEDIAWIKI_DB_PORT="5432"
	fi
fi

# Wait for the DB to come up
while [ `/bin/nc -w 1 $MEDIAWIKI_DB_HOST $MEDIAWIKI_DB_PORT < /dev/null > /dev/null; echo $?` != 0 ]; do
    echo "Waiting for database to come up at $MEDIAWIKI_DB_HOST:$MEDIAWIKI_DB_PORT..."
    sleep 1
done

export MEDIAWIKI_DB_TYPE MEDIAWIKI_DB_HOST MEDIAWIKI_DB_USER MEDIAWIKI_DB_PASSWORD MEDIAWIKI_DB_NAME

TERM=dumb php -- <<'EOPHP'
<?php
// database might not exist, so let's try creating it (just to be safe)

if ($_ENV['MEDIAWIKI_DB_TYPE'] == 'mysql') {

	$mysql = new mysqli($_ENV['MEDIAWIKI_DB_HOST'], $_ENV['MEDIAWIKI_DB_USER'], $_ENV['MEDIAWIKI_DB_PASSWORD'], '', (int) $_ENV['MEDIAWIKI_DB_PORT']);

	if ($mysql->connect_error) {
		file_put_contents('php://stderr', 'MySQL Connection Error: (' . $mysql->connect_errno . ') ' . $mysql->connect_error . "\n");
		exit(1);
	}

	if (!$mysql->query('CREATE DATABASE IF NOT EXISTS `' . $mysql->real_escape_string($_ENV['MEDIAWIKI_DB_NAME']) . '`')) {
		file_put_contents('php://stderr', 'MySQL "CREATE DATABASE" Error: ' . $mysql->error . "\n");
	}

	$mysql->close();
}
EOPHP

cd /var/www/html

: ${MEDIAWIKI_SHARED:=/data}
if [ ! -d "$MEDIAWIKI_SHARED" ]; then
    mkdir -p $MEDIAWIKI_SHARED
fi
mkdir -p "$MEDIAWIKI_SHARED/images"

# If the images directory only contains a README, then link it to
# $MEDIAWIKI_SHARED/images, creating the shared directory if necessary
if [ "$(ls images)" = "README" -a ! -L images ]; then
    rm -fr images
    ln -s "$MEDIAWIKI_SHARED/images" images
fi

# If an extensions folder exists inside the shared directory, as long as
# /var/www/html/extensions is not already a symbolic link, then replace it
if [ -d "$MEDIAWIKI_SHARED/extensions" -a ! -h /var/www/html/extensions ]; then
    echo >&2 "Found 'extensions' folder in data volume, creating symbolic link."
    rm -rf /var/www/html/extensions
    ln -s "$MEDIAWIKI_SHARED/extensions" /var/www/html/extensions
fi

# If a skins folder exists inside the shared directory, as long as
# /var/www/html/skins is not already a symbolic link, then replace it
if [ -d "$MEDIAWIKI_SHARED/skins" -a ! -h /var/www/html/skins ]; then
    echo >&2 "Found 'skins' folder in data volume, creating symbolic link."
    rm -rf /var/www/html/skins
    ln -s "$MEDIAWIKI_SHARED/skins" /var/www/html/skins
fi

# If a vendor folder exists inside the shared directory, as long as
# /var/www/html/vendor is not already a symbolic link, then replace it
if [ -d "$MEDIAWIKI_SHARED/vendor" -a ! -h /var/www/html/vendor ]; then
    echo >&2 "Found 'vendor' folder in data volume, creating symbolic link."
    rm -rf /var/www/html/vendor
    ln -s "$MEDIAWIKI_SHARED/vendor" /var/www/html/vendor
fi

# Attempt to enable SSL support if explicitly requested
if [ $MEDIAWIKI_ENABLE_SSL = true ]; then
    if [ ! -f $MEDIAWIKI_SHARED/ssl.key -o ! -f $MEDIAWIKI_SHARED/ssl.crt -o ! -f $MEDIAWIKI_SHARED/ssl.bundle.crt ]; then
        echo >&2 'error: Detected MEDIAWIKI_ENABLE_SSL flag but found no data volume';
        echo >&2 '	Did you forget to mount the volume with -v?'
        exit 1
    fi
    echo >&2 'info: enabling ssl'
    a2enmod ssl

    cp "$MEDIAWIKI_SHARED/ssl.key" /etc/apache2/ssl.key
    cp "$MEDIAWIKI_SHARED/ssl.crt" /etc/apache2/ssl.crt
    cp "$MEDIAWIKI_SHARED/ssl.bundle.crt" /etc/apache2/ssl.bundle.crt
elif [ -e "/etc/apache2/mods-enabled/ssl.load" ]; then
    echo >&2 'warning: disabling ssl'
    a2dismod ssl
fi

# If there is no LocalSettings.php, create one using maintenance/install.php
if [ ! -e "$MEDIAWIKI_SHARED/installed" -a ! -f "$MEDIAWIKI_SHARED/install.lock" ]; then
    touch $MEDIAWIKI_SHARED/install.lock
    mv LocalSettings.php LocalSettings.php.bak
	php maintenance/install.php \
		--confpath /var/www/html \
		--dbname "$MEDIAWIKI_DB_NAME" \
		--dbport "$MEDIAWIKI_DB_PORT" \
		--dbserver "$MEDIAWIKI_DB_HOST" \
		--dbtype "$MEDIAWIKI_DB_TYPE" \
		--dbuser "$MEDIAWIKI_DB_USER" \
		--dbpass "$MEDIAWIKI_DB_PASSWORD" \
		--installdbuser "$MEDIAWIKI_DB_USER" \
		--installdbpass "$MEDIAWIKI_DB_PASSWORD" \
		--server "$MEDIAWIKI_SITE_SERVER" \
		--scriptpath "" \
		--lang "$MEDIAWIKI_SITE_LANG" \
		--pass "$MEDIAWIKI_ADMIN_PASS" \
		--with-extensions \
		"$MEDIAWIKI_SITE_NAME" \
		"$MEDIAWIKI_ADMIN_USER"

    touch $MEDIAWIKI_SHARED/installed
    mv LocalSettings.php.bak LocalSettings.php
    # run update.php as extensions need to create tables
    php maintenance/update.php --quick
    rm $MEDIAWIKI_SHARED/install.lock
fi

# Install extensions
if [[ $MEDIAWIKI_EXTENSIONS ]]; then
    echo "<?php" > CustomExtensions.php
    IFS="," read -ra exts <<< "$MEDIAWIKI_EXTENSIONS"
    for i in "${exts[@]}"; do
        if [[ -f /var/www/html/extensions/$i/extension.json ]]; then
            echo "wfLoadExtension('$i');" >> CustomExtensions.php
        fi
    done
fi

# If a composer.lock and composer.json file exist, use them to install
# dependencies for MediaWiki and desired extensions, skins, etc.
if [ -e "$MEDIAWIKI_SHARED/composer.lock" -a -e "$MEDIAWIKI_SHARED/composer.json" ]; then
	curl -sS https://getcomposer.org/installer | php
	cp "$MEDIAWIKI_SHARED/composer.lock" composer.lock
	cp "$MEDIAWIKI_SHARED/composer.json" composer.json
	php composer.phar install --no-dev
fi

# If LocalSettings.php exists, then attempt to run the update.php maintenance
# script. If already up to date, it won't do anything, otherwise it will
# migrate the database if necessary on container startup. It also will
# verify the database connection is working.
if [ -e "LocalSettings.php" -a "$MEDIAWIKI_UPDATE" = 'true' -a ! -f "$MEDIAWIKI_SHARED/update.lock" ]; then
    touch $MEDIAWIKI_SHARED/update.lock
	echo >&2 'info: Running maintenance/update.php';
	php maintenance/update.php --quick --conf ./LocalSettings.php
    rm $MEDIAWIKI_SHARED/update.lock
fi

# Run custom startup tasks
chmod 755 /startuptasks.sh
/startuptasks.sh

# Ensure images folder exists
mkdir -p images

# Fix file ownership and permissions
#chown -R www-data: . # takes long time to exec in k8s, plus it's not good to set everything owned by web user
chown -R www-data: cache
chmod 755 images

#Tue to remove
rm /data/installed

exec "$@"
