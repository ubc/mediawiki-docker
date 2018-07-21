<?php
# See includes/DefaultSettings.php for all configurable settings
# and their default values, but don't forget to make changes in _this_
# file, not there.
#
# Further documentation for configuration settings may be found at:
# https://www.mediawiki.org/wiki/Manual:Configuration_settings

# Protect against web entry
if ( !defined( 'MEDIAWIKI' ) ) {
    exit;
}

function loadenv($envName, $default = "") {
    return getenv($envName) ? getenv($envName) : $default;
}


## Uncomment this to disable output compression
# $wgDisableOutputCompression = true;

$wgSitename = loadenv('MEDIAWIKI_SITE_NAME', 'MediaWiki');
if (getenv('MEDIAWIKI_META_NAMESPACE') !== false) {
    $wgMetaNamespace = loadenv('MEDIAWIKI_META_NAMESPACE', $wgSitename);
}

## The URL base path to the directory containing the wiki;
## defaults for all runtime URL paths are based off of this.
## For more information on customizing the URLs
## (like /w/index.php/Page_title to /wiki/Page_title) please see:
## https://www.mediawiki.org/wiki/Manual:Short_URL
$wgScriptPath = loadenv('MEDIAWIKI_SCRIPT_PATH');
$wgScriptExtension = ".php";
$wgArticlePath = "/$1";

## The protocol and server name to use in fully-qualified URLs
$wgServer = loadenv('MEDIAWIKI_SITE_SERVER', '//localhost');

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
$wgLogo = loadenv('MEDIAWIKI_LOGO', "$wgResourceBasePath/resources/assets/wiki.png");

## UPO means: this is also a user preference option

$wgEnableEmail = filter_var(loadenv('MEDIAWIKI_ENABLE_EMAIL', true), FILTER_VALIDATE_BOOLEAN);
$wgEnableUserEmail = filter_var(loadenv('MEDIAWIKI_ENABLE_USER_EMAIL', true), FILTER_VALIDATE_BOOLEAN); # UPO

$wgEmergencyContact = loadenv('MEDIAWIKI_EMERGENCY_CONTACT', "apache@localhost");
$wgPasswordSender = loadenv('MEDIAWIKI_PASSWORD_SENDER', "apache@localhost");

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = false; # UPO
$wgEmailAuthentication = true;

## Database settings
$wgDBtype = loadenv('MEDIAWIKI_DB_TYPE', "mysql");
$wgDBserver = loadenv('MEDIAWIKI_DB_HOST', "db");
$wgDBname = loadenv('MEDIAWIKI_DB_NAME', "mediawiki");
$wgDBuser = loadenv('MEDIAWIKI_DB_USER', "root");
$wgDBpassword = loadenv('MEDIAWIKI_DB_PASSWORD', "mediawikipass");

# MySQL specific settings
$wgDBprefix = loadenv('MEDIAWIKI_DB_PREFIX');

# MySQL table options to use during installation or update
$wgDBTableOptions = loadenv('MEDIAWIKI_DB_TABLE_OPTIONS', "ENGINE=InnoDB, DEFAULT CHARSET=binary");

# Experimental charset support for MySQL 5.0.
$wgDBmysql5 = false;

## Shared memory settings
$wgMainCacheType = constant(loadenv('MEDIAWIKI_MAIN_CACHE', 'CACHE_NONE'));
$wgMemCachedServers = json_decode(loadenv('MEDIAWIKI_MEMCACHED_SERVERS', '[]'));

## To enable image uploads, make sure the 'images' directory
## is writable, then set this to true:
$wgEnableUploads = true;
$wgUseImageMagick = true;
#$wgImageMagickConvertCommand = "/usr/bin/convert";
#$wgGenerateThumbnailOnParse = false;

# InstantCommons allows wiki to use images from https://commons.wikimedia.org
$wgUseInstantCommons = true;

# Periodically send a pingback to https://www.mediawiki.org/ with basic data
# about this MediaWiki instance. The Wikimedia Foundation shares this data
# with MediaWiki developers to help guide future development efforts.
$wgPingback = false;

## If you use ImageMagick (or any other shell command) on a
## Linux server, this will need to be set to the name of an
## available UTF-8 locale
$wgShellLocale = "C.UTF-8";

## Set $wgCacheDirectory to a writable directory on the web server
## to make your wiki go slightly faster. The directory should not
## be publically accessible from the web.
#$wgCacheDirectory = "$IP/cache";

# Site language code, should be one of the list in ./languages/data/Names.php
$wgLanguageCode = loadenv('MEDIAWIKI_LANGUAGE', "en");

$wgSecretKey = loadenv('MEDIAWIKI_SECRET_KEY', null);

# Changing this will log out all existing sessions.
$wgAuthenticationTokenVersion = "1";

# Site upgrade key. Must be set to a string (default provided) to turn on the
# web installer while LocalSettings.php is in place
$wgUpgradeKey = loadenv('MEDIAWIKI_UPGRADE_KEY', null);

## For attaching licensing metadata to pages, and displaying an
## appropriate copyright notice / icon. GNU Free Documentation
## License and Creative Commons licenses are supported so far.
$wgRightsPage = loadenv('MEDIAWIKI_RIGHTS_PAGE'); # Set to the title of a wiki page that describes your license/copyright
$wgRightsUrl = loadenv('MEDIAWIKI_RIGHTS_URL');
$wgRightsText = loadenv('MEDIAWIKI_RIGHTS_TEXT');
$wgRightsIcon = loadenv('MEDIAWIKI_RIGHTS_ICON');

# Path to the GNU diff3 utility. Used for conflict resolution.
$wgDiff3 = "/usr/bin/diff3";

## Default skin: you can change the default skin. Use the internal symbolic
## names, ie 'vector', 'monobook':
$wgDefaultSkin = loadenv('MEDIAWIKI_DEFAULT_SKIN', "vector");

# Enabled skins.
# The following skins were automatically enabled:
wfLoadSkin( 'Vector' );

// Needed to make UploadWizard work in IE, see https://phabricator.wikimedia.org/T41877
$wgApiFrameOptions = 'SAMEORIGIN';
// for UploadWizard to replace existing upload URL
$wgUploadNavigationUrl = '/wiki/Special:UploadWizard';
$wgExtensionFunctions[] = function() {
    $GLOBALS['wgUploadNavigationUrl'] = SpecialPage::getTitleFor( 'UploadWizard'  )->getLocalURL();
    return true;
};

// It is used on the top page of the UBC Wiki
$wgAllowSlowParserFunctions = true;

// enable categories for upload dialog
$wgUploadDialog = [
    'fields' => [
        'description' => true,
        'date' => true,
        'categories' => true,
    ],
    'licensemessages' => [
        'local' => 'generic-local',
        'foreign' => 'generic-foreign',
    ],
    'comment' => '',
    'format' => [
        'filepage' => '$DESCRIPTION',
        'description' => '$TEXT',
        'ownwork' => '',
        'license' => '',
        'uncategorized' => '',
    ],
];

# disable upload dialog
$wgForeignUploadTargets = [];

$wgUploadPath = loadenv('MEDIAWIKI_UPLOAD_PATH', "$wgScriptPath/images");

$wgFileExtensions = array_merge( $wgFileExtensions,
    array( 'doc', 'xls', 'docx', 'xlsx', 'pdf', 'ppt', 'pptx', 'jpg',
        'tiff', 'odt', 'odg', 'ods', 'odp', 'mp3', 'swf', 'zip', 'xml', 'svg'
));

# don't forget to change PHP and Nginx setting
$wgMaxUploadSize = 1024 * 1024 * 20;
$wgUseCopyrightUpload = "true";

$wgAllowSiteCSSOnRestrictedPages = filter_var(loadenv('MEDIAWIKI_ALLOW_SITE_CSS_ON_RESTRICTED_PAGES', false), FILTER_VALIDATE_BOOLEAN);

$wgGroupPermissions['*']['edit'] = filter_var(loadenv('MEDIAWIKI_ALLOW_ANONYMOUS_EDIT', false), FILTER_VALIDATE_BOOLEAN);

@include('CustomExtensions.php');

# some sensible defaults

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'VisualEditor') !== false) {
    # VisualEditor
    # ref: https://www.mediawiki.org/wiki/Extension:VisualEditor

    // Enable by default for everybody
    $wgDefaultUserOptions['visualeditor-enable'] = 1;

    // Optional: Set VisualEditor as the default for anonymous users
    // otherwise they will have to switch to VE
    // $wgDefaultUserOptions['visualeditor-editor'] = "visualeditor";

    // Don't allow users to disable it
    $wgHiddenPrefs[] = 'visualeditor-enable';

    // OPTIONAL: Enable VisualEditor's experimental code features
    #$wgDefaultUserOptions['visualeditor-enable-experimental'] = 1;

    # Enabling other Namespaces
    #$wgVisualEditorAvailableNamespaces = [
    #    NS_MAIN => true,
    #    NS_USER => true,
    #    102 => true,
    #    "_merge_strategy" => "array_plus"
    #];

    $wgVirtualRestConfig['modules']['parsoid'] = array(
        // URL to the Parsoid instance
        'url' => getenv('PARSOID_URL') ? getenv('PARSOID_URL') : 'http://localhost:8000',
        // Parsoid "domain" (optional)
        'domain' => getenv('PARSOID_DOMAIN') ? getenv('PARSOID_DOMAIN') : 'localhost',
        // Parsoid "prefix" (optional)
        'prefix' => getenv('PARSOID_PREFIX') ? getenv('PARSOID_PREFIX') : 'localhost'
    );
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'WikiEditor') !== false) {
    # WikiEditor
    # ref: https://www.mediawiki.org/wiki/Extension:WikiEditor

    # Enables use of WikiEditor by default but still allows users to disable it in preferences
    $wgDefaultUserOptions['usebetatoolbar'] = 1;

    # Enables link and table wizards by default but still allows users to disable them in preferences
    $wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
}

if (getenv('RESTBASE_URL')) {
    # RESTBase
    # ref: https://www.mediawiki.org/wiki/Extension:VisualEditor

    $wgVirtualRestConfig['modules']['restbase'] = [
            # used internally by wiki, so it can be docker/k8s service name
            'url' => getenv('RESTBASE_URL') ? getenv('RESTBASE_URL') : 'http://localhost:7231',
            'domain' => getenv('PARSOID_DOMAIN') ? getenv('PARSOID_DOMAIN') : 'localhost',
            'parsoidCompat' => false
        ];
    # used in browser, so it has to be public accessible, using proxy to forward request to Restbase
    $wgVisualEditorFullRestbaseURL = $wgServer . '/api/rest_';
    $wgVisualEditorRestbaseURL = $wgVisualEditorFullRestbaseURL . 'v1/page/html/';
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Math') !== false) {
    # Math
    # ref: https://www.mediawiki.org/wiki/Extension:Mat://www.mediawiki.org/wiki/Extension:Math

    $wgDefaultUserOptions['math'] = 'mathml';

    # used in browser, so it has to be public accessible, using proxy to forward request to Restbase
    $wgMathFullRestbaseURL= $wgServer . '/api/rest_';
}

# If Ldap environment variables are defined, enabled ldap function
if (getenv('LDAP_SERVER') || getenv('LDAP_BASE_DN') || getenv('LDAP_SEARCH_STRINGS') || getenv('LDAP_SEARCH_ATTRS')) {
    require_once ("$IP/extensions/LdapAuthentication/LdapAuthentication.php");
    $wgAuth = new LdapAuthenticationPlugin();

    $wgLDAPUseLocal = getenv('LDAP_USE_LOCAL') ? getenv('LDAP_USE_LOCAL') == 'true' : true;

    $wgLDAPDebug = getenv('LDAP_DEBUG') ? getenv('LDAP_DEBUG') : 0;
    $wgDebugLogGroups['ldap'] = '/tmp/mw_ldap_debug.log';

    $ldapDomain = getenv('LDAP_DOMAIN') ? getenv('LDAP_DOMAIN') : 'LOCAL';
    $wgLDAPDomainNames       = array($ldapDomain);
    $wgLDAPServerNames       = array($ldapDomain => getenv('LDAP_SERVER') ? getenv('LDAP_SERVER') : 'localhost');
    $wgLDAPEncryptionType    = array($ldapDomain => loadenv('LDAP_ENCRYPTION_TYPE', 'clear'));
    $wgMinimalPasswordLength = 1;
    $wgLDAPBaseDNs           = array($ldapDomain => getenv('LDAP_BASE_DN') ? getenv('LDAP_BASE_DN') : 'ou=Users,ou=LOCAL,dc=domain,dc=local');

    if (getenv('LDAP_SEARCH_STRINGS')) {
        $wgLDAPSearchStrings     = array($ldapDomain => getenv('LDAP_SEARCH_STRINGS'));
    }
    if (getenv('LDAP_SEARCH_ATTRS')) {
        $wgLDAPSearchAttributes  = array($ldapDomain => getenv('LDAP_SEARCH_ATTRS'));
    }

    $wgLDAPDisableAutoCreate = array($ldapDomain => getenv('LDAP_AUTO_CREATE') ? getenv('LDAP_AUTO_CREATE') == 'false' : true);
    $wgLDAPPort              = array($ldapDomain => getenv('LDAP_PORT') ? getenv('LDAP_PORT') : 389);

    if (getenv('LDAP_PROXY_AGENT')) {
        $wgLDAPProxyAgent =  array($ldapDomain => getenv('LDAP_PROXY_AGENT'));
    }
    if (getenv('LDAP_PROXY_PASSWORD')) {
        $wgLDAPProxyAgentPassword =  array($ldapDomain => getenv('LDAP_PROXY_PASSWORD'));
    }

    # set $wgLDAPLowerCaseUsername to false in order for the hook SetUsernameAttributeFromLDAP to work
    # ref: https://gerrit.wikimedia.org/r/plugins/gitiles/mediawiki/extensions/LdapAuthentication/+/master/LdapAuthenticationPlugin.php#1334
    $wgLDAPLowerCaseUsername = array($ldapDomain => false);

    # disable local wiki account creation page
    $wgGroupPermissions['*']['createaccount'] = false;
    # allow auto creation, in case LDAP auto create is enabled
    # ref: https://www.mediawiki.org/wiki/Topic:T6s2lkqumdyy0zqv
    $wgGroupPermissions['*']['autocreateaccount'] = true;

    # disable password resets entirely
    # ref: https://www.mediawiki.org/wiki/Manual:$wgPasswordResetRoutes
    $wgPasswordResetRoutes = false;
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Scribunto') !== false) {
    # Scribunto
    require_once "$IP/extensions/Scribunto/Scribunto.php";
    $wgScribuntoDefaultEngine = 'luastandalone';
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Widgets') !== false) {
    require_once "$IP/extensions/Widgets/Widgets.php";
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Maps') !== false) {
    if (loadenv('GOOGLE_MAP_API_KEY')) {
        $GLOBALS['egMapsGMaps3ApiKey'] = loadenv('GOOGLE_MAP_API_KEY');
    }
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'LiquidThreads') !== false) {
    require_once "$IP/extensions/LiquidThreads/LiquidThreads.php";
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Variables') !== false) {
    require_once "$IP/extensions/Variables/Variables.php";
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'RightFunctions') !== false) {
    require_once "$IP/extensions/RightFunctions/RightFunctions.php";
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'UserPageEditProtection') !== false) {
    require_once "$IP/extensions/UserPageEditProtection/UserPageEditProtection.php";
    $wgOnlyUserEditUserPage = true;
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Collection') !== false) {
    require_once "$IP/extensions/Collection/Collection.php";
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'DynamicPageList') !== false) {
    require_once "$IP/extensions/DynamicPageList/DynamicPageList.php";
}
@include('/conf/CustomSettings.php');

@include('CustomHooks.php');

if (filter_var(loadenv('DEBUG', false), FILTER_VALIDATE_BOOLEAN)) {
    error_reporting(-1);
    ini_set( 'display_errors', 1  );
}
