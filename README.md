# MediaWiki Docker Image

This repo is largely based on MediaWiki offical [docker repo](https://github.com/wikimedia/mediawiki-docker).

The changes are:

* Used "Vector" skin by default
* Used PHP-Apache docker image as base
* Used stable version of MediaWiki
* Customized for the production use

## Configuration options

Use the following environmental variables to generate a `LocalSettings.php` and perform automatic installation of MediaWiki. If you don't include these, you'll need to go through the installation wizard. See `Installation Wizard` below for more details. Please see [Manual:Configuration_settings](https://www.mediawiki.org/wiki/Manual:Configuration_settings) for details about what these configuration variables do.

 - `-e MEDIAWIKI_SITE_SERVER=` (**required** set this to the server host and include the protocol (and port if necessary) like `http://my-wiki:8080`; configures `$wgServer`)
 - `-e MEDIAWIKI_SITE_NAME=` (defaults to `MediaWiki`; configures `$wgSitename`)
 - `-e MEDIAWIKI_SITE_LANG=` (defaults to `en`; configures `$wgLanguageCode`)
 - `-e MEDIAWIKI_ADMIN_USER=` (defaults to `admin`; configures default administrator username)
 - `-e MEDIAWIKI_ADMIN_PASS=` (defaults to `rosebud`; configures default administrator password)
 - `-e MEDIAWIKI_UPDATE=true` (defaults to `false`; run `php maintenance/update.php`)
 - `-e MEDIAWIKI_SLEEP=` (defaults to `0`; delays startup of container, useful when using Docker Compose)
 - `-e MEDIAWIKI_EXTENSIONS` (defaults to empty; specify which extensions to enable, comma separated. Extensions are installed through docker image build)
 - `-e PARSOID_DOMAIN` (defaults to `localhost`, parsoid domain)
 - `-e RESTBASE_URL` (defaults not set, RestBase instance URL, if not set, no RestBase will be configured)
 - `-e LDAP_DOMAIN` (defaults not set, LDAP domain, e.g. CWL)
 - `-e LDAP_SERVER` (defaults not set, LDAP server address)
 - `-e LDAP_PORT` (defaults to `389`, LDAP server port)
 - `-e LDAP_ENCRYPTION_TYPE` (defaults to `clear`, LDAP connection encryption type, possible values are `clear`, `ldapi`, `tls` and `ssl`)
 - `-e LDAP_BASE_DN` (defaults to `ou=Users,ou=LOCAL,dc=domain,dc=local`, LDAP base DN for searching)
 - `-e LDAP_USER_BASE_DN` (defaults to `ou=Users,ou=LOCAL,dc=domain,dc=local`, LDAP base DN for user info)
 - `-e LDAP_SEARCH_STRINGS` (defaults not set, LDAP search string)
 - `-e LDAP_SEARCH_ATTRS` (defaults not set, LDAP search attribute)
 - `-e LDAP_PROXY_AGENT` (defaults not set, LDAP proxy agent)
 - `-e LDAP_PROXY_PASSWORD` (defaults not set, LDAP proxy agent password)
 - `-e LDAP_USERNAME_ATTR` (defaults to `cn`, LDAP attribute for user name)
 - `-e LDAP_REALNAME_ATTR` (defaults to `displayname`, LDAP attribute for real name)
 - `-e LDAP_EMAIL_ATTR` (defaults to `mail`, LDAP attribute for for email address)
 - `-e MEDIAWIKI_MAIN_CACHE` (defaults to `CACHE_NONE`, main cache)
 - `-e MEDIAWIKI_MEMCACHED_SERVERS` (defaults to `[]`, list of memcched servers, comma separated, e.g.["memcached:11211", "memcached1:11211"])
 - `-e UBC_AUTH` (defaults not set. Set o `true` to enable the UBC-specific authentication extension)
 - `-e AUTO_CREATED_USER_REDIRECT` (defaults not set.  Set it to a wiki page [e.g. `Main_page`] to redirect new users to a specific page when they first login via LDAP)

As mentioned, this will generate the `LocalSettings.php` file that is required by MediaWiki. If you mounted a shared volume (see `Shared Volume` below), the generated `LocalSettings.php` will be automatically moved to your share volume allowing you to edit it. If a `CustomSettings.php` file exists in your data file, a `require('/data/CustomSettings.php');` will be appended to the generated `LocalSettings.php` file.

## Docker Compose

```bash
docker-compose up
```
Customization can be done through the environment variables in `docker-compose.yaml`.

### Using Database Server

You can use the following environment variables for connecting to another database server:

 - `-e MEDIAWIKI_DB_TYPE=...` (defaults to `mysql`, but can also be `postgres`)
 - `-e MEDIAWIKI_DB_HOST=...` (defaults to the address of the linked database container)
 - `-e MEDIAWIKI_DB_PORT=...` (defaults to the port of the linked database container or to the default for specified db type)
 - `-e MEDIAWIKI_DB_USER=...` (defaults to `root` or `postgres` based on db type being `mysql`, or `postgres` respsectively)
 - `-e MEDIAWIKI_DB_PASSWORD=...` (defaults to the password of the linked database container)
 - `-e MEDIAWIKI_DB_NAME=...` (defaults to `mediawiki`)
 - `-e MEDIAWIKI_DB_SCHEMA`... (defaults to `mediawiki`, applies only to when using postgres)

If the `MEDIAWIKI_DB_NAME` specified does not already exist on the provided MySQL
server, it will be created automatically upon container startup, provided
that the `MEDIAWIKI_DB_USER` specified has the necessary permissions to create
it.

To use with an external database server, use `MEDIAWIKI_DB_HOST` (along with
`MEDIAWIKI_DB_USER` and `MEDIAWIKI_DB_PASSWORD` if necessary):

    docker run --name some-mediawiki \
        -e MEDIAWIKI_DB_HOST=10.0.0.1
        -e MEDIAWIKI_DB_PORT=3306 \
        -e MEDIAWIKI_DB_USER=app \
        -e MEDIAWIKI_DB_PASSWORD=secure \
        wikimedia/mediawiki

## Shared Volume

If provided mount a shared volume using the `-v` argument when running `docker run`, the mediawiki container will automatically look for a `LocalSettings.php` file and `images`, `skins` and `extensions` folders. When found symbolic links will be automatically created to the respsective file or folder to replace the ones included with the default MediaWiki install. This allows you to easily configure (`LocalSettings.php`), backup uploaded files (`images`), and customize (`skins` and `extensions`) your instance of MediaWiki.

It is highly recommend you mount a shared volume so uploaded files and images will be outside of the docker container.

By default the shared volume must be mounted to `/data` on the container, you can change this using by using `-e MEDIAWIKI_SHARED=/new/data/path`.

Additionally if a `composer.lock` **and** a `composer.json` are detected, the container will automatically download [composer](https://getcomposer.org) and run `composer install`. Composer can be used to install additional extensions, skins and dependencies.

## Accessing MediaWiki

Access it via `http://localhost:8080` or `http://host-ip:8080` in a browser.

Database frontend Adminer is available at `http://localhost:8089`.


## Setting up instance for development

First startup the application for the first time with:

```bash
docker-compose up -d
```

Next after startup run the following to add `the user_cwl_extended_account_data` table

```bash
docker cp ./dev/add_table.sql mediawiki-docker_db_1:/add_table.sql
docker exec -it mediawiki-docker_db_1 /bin/bash -c "mysql -u root -ppassword mediawiki < /add_table.sql"
```

Next you need to uncomment the line `- ./LocalSettings.php:/var/www/html/LocalSettings.php` in the docker compose file.

Finally restart all the containers with:

```bash
docker-compose down
docker-compose up -d
```

### Setting up SAML2 auth using the SimpleSAMLphp Extension.

The dev docker compose configuration expects the wiki to be located at
`wiki.docker` and the IDP to be located at `idp.docker`. This can be easily
configured by editing the hosts file and mapping both `wiki.docker` and
`idp.docker` to `127.0.0.1`.

#### Generate Key & Cert

**WARNING** the certs provided in `docker/simplesamlphp/sp/cert/` must NOT be
used in any kind of prod environment. They are only there for easier setup of
the docker compose dev environment.

To generate your own cert and key:

```bash
openssl req -newkey rsa:3072 -new -x509 -days 3652 -nodes -out wiki-sp.crt -keyout wiki-sp.pem
```

The key and cert can replace the ones in `docker/simplesamlphp/sp/cert/` which
will get mounted into the simplesamlphp container's
/var/www/simplesamlphp/cert/ directory.

#### SP Required Environment Variables

Deployment of the SimpleSAMLphp SP is required if you want to use the
SimpleSAMLphp extension. Note the IDP provided in docker compose is only for
development purposes.

The SP pulls metadata from the target IDP's metadata URL.

Required SP environment variables:

* SIMPLESAMLPHP_SECRET_SALT - Cryptographically secured random string used for salting purposes.
* SIMPLESAMLPHP_ADMIN_PASSWORD - Password for the default admin user.
* SIMPLESAMLPHP_MEMCACHED_SERVER - SimpleSAMLphp's SP cannot use the cookie cache as the wiki side SimpleSAMLphp extension will conflict with it. So we need to use a separate cache. For this purpose, we can just use the same Memcached server that the wiki uses.
* SIMPLESAMLPHP_TRUSTED_DOMAIN - Enter the wiki's domain here so that the SP knows it is safe.
* SIMPLESAMLPHP_BASEURL - Base URL for the SP (no path). The SP needs to share the same domain as the wiki (or you run into cookie domain issues), so the base URL should just be the wiki domain with an http:// or https:// prefix. This config lets SimpleSAMLphp knows it's running externally on https even if internally the backend server is plain http, such as when behind a load balancer/reverse proxy.
* SIMPLESAMLPHP_BASEURLPATH - Base URL plus the path for the SP.
* SIMPLESAMLPHP_SP_ENTITY_ID - The identifier that the SP uses to identify itself
* SIMPLESAMLPHP_IDP_ENTITY_ID - The target IDP's identifier.
* SIMPLESAMLPHP_IDP_METADATA_URL - URL where we can get the IDP's metadata.
* SIMPLESAMLPHP_CRON_SECRET - Random alphanumeric string for cron security.

Optional SP environment variables:

* SIMPLESAMLPHP_DEV - This turns on dev mode which enables the admin interface at `<SIMPLESAMLPHP_BASEURL>/module.php/admin/`. It also allows SIMPLESAMLPHP_SECRET_SALT to default to 'secretsalt' if unset and SIMPLESAMLPHP_ADMIN_PASSWORD to default to 'admin' if unset.

### Adding new LDAP users

You can connect to the LDAP container using your preferred LDAP GUI using `localhost:1389` with login `cn=admin,dc=example,dc=org` and password `admin`.

When adding a new user, make sure to use `simpleSecurityObject`, `inetOrgPerson`, and `ubcEdu` classes.

## Customization with LDAP authentication enabled

Customize the login button by modifying the page `MediaWiki:Pluggableauth-loginbutton-label`.  The default is "Log in with PluggableAuth".

Customize the login help message by modifying the page `MediaWiki:Userlogin-helplink2` and `MediaWiki:Helplogin-url`.  The default is a hyperlink "Help with logging in" that links to mediawiki help page.

Customize the help message on `Preferences` page about email addresses by editing the page `MediaWiki:Prefs-help-email`.  The default help messages mentioned email addresses are used for password reset, which is irrelevant if mediawiki is setup with LDAP authentication.

## Custom Caliper actor data

See the [mediawiki-extensions-caliper](https://github.com/ubc/mediawiki-extensions-caliper/blob/master/caliper/actor.php) repo's `CaliperActor` object for the default logged in and logged out users.

You can customize the Caliper actor by using the `SetCaliperActorObject` hook. This container has uses this hook with the `SetCaliperActor` function inside of `CustomHooks.php`.

By default, the `SetCaliperActor` function will use UBC `puid` for the identifier and `CALIPER_LDAP_ACTOR_HOMEPAGE` environment variable as the base string so the actor identifier will take the form of `CaliperLDAPActorHomepage/LDAP_PUID` (ex: `https://www.ubc.ca/SOME_PUID`). you can instead remove this function and create your own depending on your institution needs, deployment settings, and/or authorization methods.

## Debugging with Containers

To change the files in container:
```bash
docker exec -it CONTAINER_ID share
vi FILE_TO_CHANGE
```
You may need to restart the container to load the change, use:
```bash
docker-compose restart SERIVCE_NAME
```
where the SERIVCE_NAME can be any service in docker-compose, e.g. nodeservices, web, db, etc. The changes in the container will persist.

## Version and Release Tags

The `REL*` branches track the upstream Mediawiki released versions. When something is updated in this repo and is ready to be deployed, a new tag should be created and the tag name should in the format of `BRANCH_NAME + BUILD NUMBER`, e.g. REL1_30_B2 or REL1_31_B5. The `BUILD_NUMBER` should be increased sequentially.

The same rules apply to node-services repo as well.

## Upgrading

When upgrading to a newer version, we can run the web updater, this requires
setting an upgrade key. A default upgrade key with value 'value' is set in
docker-compose.yml

    http://localhost:8080/mw-config/index.php?page=Upgrade

There is also an maintenance update script that must be run, this can be done manually:

    php maintenance/update.php

Alternatively, setting the env var `MEDIAWIKI_UPDATE` to true will run the
maintenance update script on container startup.

## Admin Access

Promote a existing user to have all admin permissions:

    php maintenance/createAndPromote.php Admin1 --force --bureaucrat --sysop --interface-admin
