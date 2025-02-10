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
$wgDisableOutputCompression = true;

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
$wgArticlePath = "/$1";

## The protocol and server name to use in fully-qualified URLs
$wgServer = loadenv('MEDIAWIKI_SITE_SERVER', '//localhost');

## The URL path to static resources (images, scripts, etc.)
$wgResourceBasePath = $wgScriptPath;

## The URL path to the logo.  Make sure you change this from the default,
## or else you'll overwrite your logo when you upgrade!
if (getenv('MEDIAWIKI_LOGO_ICON') !== false) {
    $wgLogos = [
        # square 50x50 pixel SVG or 100x100 pixel regular image, used by new themes
        'icon' => loadenv('MEDIAWIKI_LOGO_ICON'),
        # used by old themes, will be deprecated in the future
        '1x' => loadenv('MEDIAWIKI_LOGO_LEGACY1X',
                        "$wgResourceBasePath/resources/assets/wiki.png"),
        '2x' => loadenv('MEDIAWIKI_LOGO_LEGACY2X',
                        "$wgResourceBasePath/resources/assets/wiki.png"),
    ];
}

## UPO means: this is also a user preference option

$wgEnableEmail = filter_var(loadenv('MEDIAWIKI_ENABLE_EMAIL', true), FILTER_VALIDATE_BOOLEAN);
$wgEnableUserEmail = filter_var(loadenv('MEDIAWIKI_ENABLE_USER_EMAIL', true), FILTER_VALIDATE_BOOLEAN); # UPO

$wgEmergencyContact = loadenv('MEDIAWIKI_EMERGENCY_CONTACT', "apache@localhost");
$wgPasswordSender = loadenv('MEDIAWIKI_PASSWORD_SENDER', "apache@localhost");

# Disable Job execution on page requests (can be setup via cron jobs)
$wgJobRunRate = 0;

$wgEnotifUserTalk = false; # UPO
$wgEnotifWatchlist = true; # UPO
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

# setup debug environment
if (filter_var(loadenv('DEBUG', false), FILTER_VALIDATE_BOOLEAN)) {
    error_reporting(-1);
    ini_set( 'display_errors', 1  );
    $wgShowExceptionDetails = true;
    $wgCacheDirectory = false;
    $wgCachePages = false;
    $wgDebugLogFile = "/tmp/mw-debug-{$wgDBname}.log";
    $wgDebugLogGroups = [
        "SimpleSAMLphp" => "/tmp/mw-debug-SimpleSAMLphp.log",
        "PluggableAuth" => "/tmp/mw-debug-PluggableAuth.log",
        "UBCAuth" => "/tmp/mw-debug-UBCAuth.log",
    ];
}

## Shared memory settings
$mainCache = loadenv('MEDIAWIKI_MAIN_CACHE', 'CACHE_NONE');
$wgMainCacheType = defined("$mainCache") ? constant($mainCache) : $mainCache;
switch ($wgMainCacheType) {
    case CACHE_MEMCACHED:
        $wgMemCachedServers = json_decode(loadenv('MEDIAWIKI_MEMCACHED_SERVERS', '[]'));
        break;
    case 'redis':
        $wgObjectCaches['redis'] = [
            'class' => 'RedisBagOStuff',
            'servers' => [
                loadenv('MEDIAWIKI_REDIS_HOST').':'.loadenv('MEDIAWIKI_REDIS_PORT', 6379)
            ],
            'persistent' => filter_var(loadenv('MEDIAWIKI_REDIS_PERSISTENT', false), FILTER_VALIDATE_BOOLEAN)

        ];
        if (!empty($redis_pwd = loadenv('MEDIAWIKI_REDIS_PASSWORD'))) {
            $wgObjectCaches['redis']['password'] = $redis_pwd;
        }
        break;
}

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
$wgCacheDirectory = loadenv('MEDIAWIKI_CACHE_DIRECTORY', false);

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
$wgDefaultSkin = loadenv('MEDIAWIKI_DEFAULT_SKIN', "vector-2022");

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

// UploadWizard License Customization
$wgUploadWizardConfig = array(
  'feedbackLink' => false,
  'alternativeUploadToolsPage' => false,
  'tutorial' => [
     // UBC edition of copyright tutorial
     'template' => 'File:Ubc_copyright_tutorial.png'
  ],
  'licenses' => [
    'cc-by-sa-4.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-sa-4.0',
        'icons' => [ 'cc-by', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-sa/4.0/',
        'languageCodePrefix' => 'deed.'
    ],

    // 2.5 Attribution Canada
    'cc-by-2.5-ca' => [
        'msg' => 'mwe-upwiz-license-cc-by-2.5-ca',
        'icons' => [ 'cc-by', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by/2.5/ca/',
        'templates' => [ 'cc-by-2.5-ca' ]
    ],

    'cc-by-2.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-2.0',
        'icons' => [ 'cc-by' ],
        'url' => '//creativecommons.org/licenses/by/2.0/',
        'templates' => [ 'cc-by-2.0' ]
    ],

    // 2.5 SA Canada
    'cc-by-sa-2.5-ca' => [
        'msg' => 'mwe-upwiz-license-cc-by-sa-2.5-ca',
        'icons' => [ 'cc-by', 'cc-sa'],
        'url' => '//creativecommons.org/licenses/by-sa/2.5/ca/',
        'templates' => [ 'cc-by-sa-2.5-ca' ]
    ],

    'cc-by-sa-2.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-sa-2.0',
        'icons' => [ 'cc-by', 'cc-sa'],
        'url' => '//creativecommons.org/licenses/by-sa/2.0/',
        'templates' => [ 'cc-by-sa-2.0' ]
    ],

    'cc-by-nc-4.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-4.0',
        'icons' => [ 'cc-by', 'cc-nc' ],
        'url' => '//creativecommons.org/licenses/by-nc/4.0/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-3.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-3.0',
        'icons' => [ 'cc-by', 'cc-nc' ],
        'url' => '//creativecommons.org/licenses/by-nc/3.0/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-2.5-ca' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-2.5-ca',
        'icons' => [ 'cc-by', 'cc-nc' ],
        'url' => '//creativecommons.org/licenses/by-nc/2.5/ca/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-2.5' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-2.5',
        'icons' => [ 'cc-by', 'cc-nc' ],
        'url' => '//creativecommons.org/licenses/by-nc/2.5/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-2.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-2.0',
        'icons' => [ 'cc-by', 'cc-nc' ],
        'url' => '//creativecommons.org/licenses/by-nc/2.0/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-sa-4.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-sa-4.0',
        'icons' => [ 'cc-by', 'cc-nc', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-nc-sa/4.0/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-sa-3.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-sa-3.0',
        'icons' => [ 'cc-by', 'cc-nc', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-nc-sa/3.0/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-sa-2.5-ca' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-sa-2.5-ca',
        'icons' => [ 'cc-by', 'cc-nc', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-nc-sa/2.5/ca/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-sa-2.5' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-sa-2.5',
        'icons' => [ 'cc-by', 'cc-nc', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-nc-sa/2.5/',
        'languageCodePrefix' => 'deed.'
    ],

    'cc-by-nc-sa-2.0' => [
        'msg' => 'mwe-upwiz-license-cc-by-nc-sa-2.0',
        'icons' => [ 'cc-by', 'cc-nc', 'cc-sa' ],
        'url' => '//creativecommons.org/licenses/by-nc-sa/2.0/',
        'languageCodePrefix' => 'deed.'
    ],

    // Copyright Canadian Gov
    'cr-cdn-gov' => [
        'msg' => 'mwe-upwiz-license-cr-cdn-gov',
        //'icons' => array( 'cc-by'),
        'templates' => [ 'cr-cdn-gov' ]
    ],

    // Expired Canada
    'cr-cdn-exp' => [
        'msg' => 'mwe-upwiz-license-cr-cdn-exp',
        'templates' => [ 'cr-cdn-exp' ]
    ],

    // UBC
    'cr-ubc' => [
        'msg' => 'mwe-upwiz-license-cr-ubc',
        'templates' => [ 'cr-ubc' ]
    ],
    'attribution' => [
        'msg' => 'mwe-upwiz-license-attribution'
    ],
    'none' => [
        'msg' => 'mwe-upwiz-license-none',
        'templates' => [ 'subst:uwl' ]
    ],
    'generic' => [
        'msg' => 'mwe-upwiz-license-generic',
        'templates' => [ 'Generic' ]
    ]
  ],
  'licensing' => [
    'ownWork' => array(
      'type' => 'or',
      'template' => 'self',
      'defaults' => 'cc-by-sa-4.0',
      'licenses' => array(
        'cc-by-sa-4.0',
        'cc-by-4.0',
        'cc-zero',
        'cc-by-nc-4.0',
        'cc-by-nc-sa-4.0'
      )
    ),
    'thirdParty' => [
      'type' => 'or',
      'licenseGroups' => [
        [
              // This should be a list of all CC licenses we can reasonably expect to find around the web
              'head' => 'mwe-upwiz-license-cc-head',
              'subhead' => 'mwe-upwiz-license-cc-subhead',
              'licenses' => [
                  'cc-by-sa-4.0',
                  'cc-by-sa-3.0',
                  'cc-by-sa-2.5-ca',
                  'cc-by-sa-2.5',
                  'cc-by-sa-2.0',
                  'cc-by-4.0',
                  'cc-by-3.0',
                  'cc-by-2.5-ca',
                  'cc-by-2.5',
                  'cc-by-2.0',
                  'cc-zero',
                  'cc-by-nc-4.0',
                  'cc-by-nc-3.0',
                  'cc-by-nc-2.5-ca',
                  'cc-by-nc-2.5',
                  'cc-by-nc-2.0',
                  'cc-by-nc-sa-4.0',
                  'cc-by-nc-sa-3.0',
                  'cc-by-nc-sa-2.5-ca',
                  'cc-by-nc-sa-2.5',
                  'cc-by-nc-sa-2.0'
              ]
        ],
        // Canadian Gov license
        [
           'head' => 'mwe-upwiz-license-cdngov-head',
           'licenses' => array(
              'cr-cdn-gov'
           )
        ],
        // Expire Canadian Public Domain
        [
              'head' => 'mwe-upwiz-license-cr-cdn-exp-head',
              'licenses' => array(
                 'cr-cdn-exp'
              )
        ],
        // UBC license
        [
            'head' => 'mwe-upwiz-license-ubc-head',
            'licenses' => array(
                'cr-ubc'
            )
        ],
      ]
    ]
  ]
);

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

# allow admin/sysop class to rename user
$wgGroupPermissions['sysop']['renameuser'] = true;

# Hide renameuser logs from Special:Log
$wgFilterLogTypes['renameuser'] = true;
# Restrict access to renameuser log type.
# Special:Log/renameuser is accessible only to those with renameuser permission.
# This should also stop new renameuser logs being added to Special:RecentChanges. Existing rename logs in RecentChanges can be cleared by running "php maintenance/rebuildrecentchanges.php"
$wgLogRestrictions['renameuser'] = 'renameuser';

if (getenv('SMTP_HOST')) {
    $wgSMTP = [];
    $wgSMTP['host'] = loadenv('SMTP_HOST');
    if (getenv('SMTP_HOST_ID')) {
        $wgSMTP['IDHost'] = loadenv('SMTP_HOST_ID');
    }
    $wgSMTP['port'] = loadenv('SMTP_PORT', 25);

    if (getenv('SMTP_USER')) {
       $wgSMTP['auth'] = true;
       $wgSMTP['username'] = loadenv('SMTP_USER');
       $wgSMTP['password'] = loadenv('SMTP_PASSWORD');
    }
}

$wgReadOnly = loadenv('MEDIAWIKI_READONLY', null);

$wgLocalisationCacheConf = array(
    'class' => 'LocalisationCache',
    'store' => loadenv('MEDIAWIKI_LOCALISATION_CACHE_STORE', 'detect'),
    'storeClass' => false,
    'storeDirectory' => false,
    'manualRecache' => filter_var(loadenv('MEDIAWIKI_LOCALISATION_CACHE_MANUALRECACHE', false), FILTER_VALIDATE_BOOLEAN),
);

$wgEnableBotPasswords = filter_var(loadenv('MEDIAWIKI_ENABLE_BOT_PASSWORDS', true), FILTER_VALIDATE_BOOLEAN);

# allow canvas to embed wiki pages
$wgEditPageFrameOptions = 'allow-from https://canvas.ubc.ca/';

@include('CustomExtensions.php');

# some sensible defaults

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'VisualEditor') !== false) {
    # VisualEditor
    # ref: https://www.mediawiki.org/wiki/Extension:VisualEditor
    wfLoadExtension( 'VisualEditor' );
    $wgGroupPermissions['*']['writeapi'] = true;
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

    # https://www.mediawiki.org/wiki/Parsoid#Linking_a_developer_checkout_of_Parsoid
    #$PARSOID_INSTALL_DIR = 'vendor/wikimedia/parsoid'; # bundled copy
    #wfLoadExtension( 'Parsoid', "$PARSOID_INSTALL_DIR/extension.json" );
    wfLoadExtension( 'Parsoid', "$IP/vendor/wikimedia/parsoid/extension.json" );
    // $wgVirtualRestConfig['modules']['parsoid'] = array(
    //     // URL to the Parsoid instance
    //     'url' => getenv('PARSOID_URL') ? getenv('PARSOID_URL') : 'http://localhost:8000',
    //     // Parsoid "domain" (optional)
    //     'domain' => getenv('PARSOID_DOMAIN') ? getenv('PARSOID_DOMAIN') : 'localhost',
    //     // Parsoid "prefix" (optional)
    //     'prefix' => getenv('PARSOID_PREFIX') ? getenv('PARSOID_PREFIX') : 'localhost'
    // );
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'WikiEditor') !== false) {
    # WikiEditor # ref: https://www.mediawiki.org/wiki/Extension:WikiEditor

    # Enables use of WikiEditor by default but still allows users to disable it in preferences
    $wgDefaultUserOptions['usebetatoolbar'] = 1;

    # Enables link and table wizards by default but still allows users to disable them in preferences
    $wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;
}

// disable restbase for now as it broken after 1.39 upgrade
/*if (getenv('RESTBASE_URL')) {
    # RESTBase
    # ref: https://www.mediawiki.org/wiki/Extension:VisualEditor#RESTBase_setup_for_switching
    $wgVisualEditorParsoidAutoConfig = false;
    $wgVirtualRestConfig['modules']['restbase'] = [
            # used internally by wiki, so it can be docker/k8s service name
            'url' => getenv('RESTBASE_URL') ? getenv('RESTBASE_URL') : 'http://localhost:7231',
            'domain' => getenv('PARSOID_DOMAIN') ? getenv('PARSOID_DOMAIN') : 'localhost',
            'parsoidCompat' => false
        ];
    # used in browser, so it has to be public accessible, using proxy to forward request to Restbase
    $wgVisualEditorFullRestbaseURL = $wgServer . '/api/rest_';
    $wgVisualEditorRestbaseURL = $wgVisualEditorFullRestbaseURL . 'v1/page/html/';
}*/

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Math') !== false) {
    # Math
    # ref: https://www.mediawiki.org/wiki/Extension:Mat://www.mediawiki.org/wiki/Extension:Math

    $wgDefaultUserOptions['math'] = 'mathml';

    # used in browser, so it has to be public accessible, using proxy to forward request to Restbase
    $wgMathFullRestbaseURL= $wgServer . '/api/rest_';
}

if (getenv('LDAP_SERVER') || getenv('LDAP_BASE_DN') || getenv('LDAP_SEARCH_STRINGS') || getenv('LDAP_SEARCH_ATTRS')) {
    // load and configure LDAP Provider
    wfLoadExtension( 'LDAPProvider' );

    // define our LDAP authentication domain
    global $ubcLDAPDomain;
    $ubcLDAPDomain = getenv('LDAP_DOMAIN');
    $LDAPProviderDomainConfigProvider = function() use($ubcLDAPDomain) {
        $config = [
            $ubcLDAPDomain => [
                'connection' => [
                    "server" => getenv('LDAP_SERVER') ? getenv('LDAP_SERVER') : 'localhost',
                    "port" => getenv('LDAP_PORT') ? getenv('LDAP_PORT') : 389,
                    "enctype" => getenv('LDAP_ENCRYPTION_TYPE') ? getenv('LDAP_ENCRYPTION_TYPE') : 'clear',
                    "user" => getenv('LDAP_PROXY_AGENT') ? getenv('LDAP_PROXY_AGENT') : '',
                    "pass" => getenv('LDAP_PROXY_PASSWORD') ? getenv('LDAP_PROXY_PASSWORD') : '',
                    "basedn" => getenv('LDAP_BASE_DN') ? getenv('LDAP_BASE_DN') : 'ou=Users,ou=LOCAL,dc=domain,dc=local',
                    "userbasedn" => getenv('LDAP_USER_BASE_DN') ? getenv('LDAP_USER_BASE_DN') : 'ou=Users,ou=LOCAL,dc=domain,dc=local',
                    "searchstring" => getenv('LDAP_SEARCH_STRINGS') ? getenv('LDAP_SEARCH_STRINGS') : '',
                    "searchattribute" => getenv('LDAP_SEARCH_ATTRS') ? getenv('LDAP_SEARCH_ATTRS') : 'cn',
                    "usernameattribute" => getenv('LDAP_USERNAME_ATTR') ? getenv('LDAP_USERNAME_ATTR') : 'cn',
                    "realnameattribute" => getenv('LDAP_REALNAME_ATTR') ? getenv('LDAP_REALNAME_ATTR') : 'displayname',
                    "emailattribute" => getenv('LDAP_EMAIL_ATTR') ? getenv('LDAP_EMAIL_ATTR') : 'mail',
                ]
            ]
        ];

        return new \MediaWiki\Extension\LDAPProvider\DomainConfigProvider\InlinePHPArray( $config );
    };

    // load LDAP authentication extensions
    wfLoadExtension( 'PluggableAuth' );
    wfLoadExtension( 'LDAPAuthentication2' );

    # do not allow "local" pseudo-domain login against local user db
    $LDAPAuthentication2AllowLocalLogin = false;
    $wgPluggableAuth_EnableLocalLogin = false;

    $wgPluggableAuth_Config['CWL Log In'] = [
        'plugin' => 'LDAPAuthentication2',
        'data' => [
            'domain' => $ubcLDAPDomain
        ]
    ];

    # disable local wiki account creation page
    $wgGroupPermissions['*']['createaccount'] = false;
    # allow auto creation
    $wgGroupPermissions['*']['autocreateaccount'] = true;

    # disable password resets entirely
    # ref: https://www.mediawiki.org/wiki/Manual:$wgPasswordResetRoutes
    $wgPasswordResetRoutes = array(
        'username' => false,
        'email' => false,
    );

    # enable local properties so users can edit their real name and email
    # ref: https://www.mediawiki.org/wiki/Extension:PluggableAuth
    $wgPluggableAuth_EnableLocalProperties = true;

    // extension for UBC-specific authentication
    if ( filter_var( getenv( 'UBC_AUTH_ENABLED' ), FILTER_VALIDATE_BOOLEAN ) ) {
        wfLoadExtension( 'UBCAuth' );
    }
}

if (filter_var( getenv( 'SIMPLESAMLPHP_ENABLED' ), FILTER_VALIDATE_BOOLEAN )) {
    wfLoadExtension( 'PluggableAuth' );
    wfLoadExtension( 'SimpleSAMLphp' );
    $wgSimpleSAMLphp_InstallDir = '/var/www/simplesamlphp';
    $wgPluggableAuth_EnableLocalLogin = false;
    # allow background post request logout instead of requiring an additional
    # user form post submit click. Seems to work despite possible
    # incompatibility with SLO noted in docs.
    $wgPluggableAuth_EnableFastLogout = true;

    $wgPluggableAuth_Config['CWL Log In'] = [
        'plugin' => 'SimpleSAMLphp',
        'data' => [
            'authSourceId' => 'wiki-sp',
            # standardized attributes with commonly used OIDs
            # uid attribute, which in Shib is the CWL login name
            'usernameAttribute' => 'urn:oid:0.9.2342.19200300.100.1.1',
            # displayName attribute, aka preferred name
            'realNameAttribute' => 'urn:oid:2.16.840.1.113730.3.1.241',
            # mail attribute, email address
            'emailAttribute' => 'urn:oid:0.9.2342.19200300.100.1.3',
            # UBCAuth required attributes:
            # eduPersonAffiliation, an array of (staff, student, faculty, etc)
            'eduPersonAffiliationAttribute' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.1',
            # ubc's puid, non-standard attribute/OID
            'puidAttribute' => 'urn:mace:dir:attribute-def:ubcEduCwlPuid',
        ]
    ];

    # disable local wiki account creation page
    $wgGroupPermissions['*']['createaccount'] = false;
    # allow auto creation
    $wgGroupPermissions['*']['autocreateaccount'] = true;

    # disable password resets entirely
    # ref: https://www.mediawiki.org/wiki/Manual:$wgPasswordResetRoutes
    $wgPasswordResetRoutes = array(
        'username' => false,
        'email' => false,
    );

    # enable local properties so users can edit their real name and email
    # ref: https://www.mediawiki.org/wiki/Extension:PluggableAuth
    $wgPluggableAuth_EnableLocalProperties = true;

    if ( filter_var( getenv( 'UBC_AUTH_ENABLED' ), FILTER_VALIDATE_BOOLEAN ) ) {
        wfLoadExtension( 'UBCAuth' );
        $wgSimpleSAMLphp_MandatoryUserInfoProviders['username'] = [
            'class' => 'MediaWiki\Extension\UBCAuth\UsernameInfoProvider'
        ];
    }
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Scribunto') !== false) {
    # Scribunto
    wfLoadExtension( 'Scribunto' );
    $wgScribuntoDefaultEngine = 'luastandalone';
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Widgets') !== false) {
    wfLoadExtension( 'Widgets' );
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Maps') !== false) {
    if (loadenv('GOOGLE_MAP_API_KEY')) {
        $egMapsGMaps3ApiKey = loadenv('GOOGLE_MAP_API_KEY');
    }
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'LiquidThreads') !== false) {
    wfLoadExtension( 'LiquidThreads' );
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Variables') !== false) {
    wfLoadExtension( 'Variables' );
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'RightFunctions') !== false) {
   wfLoadExtension( 'RightFunctions' );
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'UserPageEditProtection') !== false) {
    wfLoadExtension( 'UserPageEditProtection' );
    $wgOnlyUserEditUserPage = true;
}

/*if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Collection') !== false) {
    require_once "$IP/extensions/Collection/Collection.php";
}*/

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'DynamicPageList') !== false) {
    wfLoadExtension( 'DynamicPageList' );
    $wgDplSettings['maxResultCount'] = 800;
}

/*if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'googleAnalytics') !== false && loadenv('GOOGLE_ANALYTICS_UA')) {
    require_once "$IP/extensions/googleAnalytics/googleAnalytics.php";
    // Replace xxxxxxx-x with YOUR GoogleAnalytics UA number
    $wgGoogleAnalyticsAccount = loadenv('GOOGLE_ANALYTICS_UA');
    // Add HTML code for any additional web analytics (can be used alone or with $wgGoogleAnalyticsAccount)
    #$wgGoogleAnalyticsOtherCode = '<script type="text/javascript" src="https://analytics.example.com/tracking.js"></script>';

    // Optional configuration (for defaults see googleAnalytics.php)
    // Store full IP address in Google Universal Analytics (see https://support.google.com/analytics/answer/2763052?hl=en for details)
    $wgGoogleAnalyticsAnonymizeIP = false;
    // Array with NUMERIC namespace IDs where web analytics code should NOT be included.
    #$wgGoogleAnalyticsIgnoreNsIDs = array(500);
    // Array with page names (see magic word Extension:Google Analytics Integration) where web analytics code should NOT be included.
    #$wgGoogleAnalyticsIgnorePages = array('ArticleX', 'Foo:Bar');
    // Array with special pages where web analytics code should NOT be included.
    $wgGoogleAnalyticsIgnoreSpecials = array( 'Userlogin', 'Userlogout', 'Preferences', 'ChangePassword', 'OATH');
    // Use 'noanalytics' permission to exclude specific user groups from web analytics, e.g.
    $wgGroupPermissions['sysop']['noanalytics'] = true;
    $wgGroupPermissions['bot']['noanalytics'] = true;
    // To exclude all logged in users give 'noanalytics' permission to 'user' group, i.e.
    #$wgGroupPermissions['user']['noanalytics'] = true;

    # Google Analyics Metrics
    $t = loadenv('GOOGLE_ANALYTICS_METRICS_ALLOWED', '*');
    $wgGoogleAnalyticsMetricsAllowed = $t == '*' ? '*' : explode(',', $t);
    $wgGoogleAnalyticsMetricsPath = loadenv('GOOGLE_ANALYTICS_METRICS_PATH', NULL);
    $wgGoogleAnalyticsMetricsViewId = loadenv('GOOGLE_ANALYTICS_METRICS_VIEWID', '');
}*/

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'GTag') !== false && loadenv('GOOGLE_ANALYTICS_ID')) {
    wfLoadExtension( 'GTag' );
    $wgGTagAnalyticsId = loadenv('GOOGLE_ANALYTICS_ID');
}


# setup caliper settings if enabled
if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'caliper') !== false && loadenv('CALIPER_HOST') && loadenv('CALIPER_API_KEY')) {
    $wgCaliperHost = loadenv('CALIPER_HOST');
    $wgCaliperAPIKey = loadenv('CALIPER_API_KEY');
    $wgCaliperAppBaseUrl = loadenv('CALIPER_BASE_URL', null);
    $wgCaliperUseJobQueue = loadenv('CALIPER_USE_JOB_QUEUE', true);
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'LinkTarget') !== false) {
    wfLoadExtension( 'LinkTarget' );
    $wgLinkTargetParentClasses = array('linkexternal');
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), '3D') !== false) {
    wfLoadExtension( '3D' );
    wfLoadExtension( 'MultimediaViewer' );
    $wgTrustedMediaFormats[] = 'application/sla';
    $wgFileExtensions[] = 'stl';
}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'TimedMediaHandler') !== false) {
    wfLoadExtension( 'TimedMediaHandler' );
}


if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'SmiteSpam') !== false) {
    wfLoadExtension( 'SmiteSpam' );

    ##SmiteSpam assigns a probability, from 0 to 1, that any specific page holds spam.
    $wgSmiteSpamThreshold = 0.9;
    $wgSmiteSpamIgnoreSmallPages = false;
    $wgSmiteSpamIgnorePagesWithNoExternalLinks = false;

}

if (getenv('MEDIAWIKI_EXTENSIONS') && strpos(getenv('MEDIAWIKI_EXTENSIONS'), 'Nuke') !== false) {
    wfLoadExtension( 'Nuke' );
}


@include('/conf/CustomSettings.php');

@include('CustomHooks.php');

# UBC Wiki Books - a setting to allow books to be saved as collection of pages
$wgGroupPermissions['user']['collectionsaveascommunitypage'] = true;
$wgGroupPermissions['user']['collectionsaveasuserpage'] = true;

# redirect auto created users to specific page when they login for the first time
if ( getenv( 'AUTO_CREATED_USER_REDIRECT' ) ) {
    wfLoadExtension( 'AutoCreatedUserRedirector' );
    $wgAutoCreatedUserRedirect = getenv( 'AUTO_CREATED_USER_REDIRECT' );
    $wgAutoBlockedUserRedirect = getenv( 'AUTO_BLOCKED_USER_REDIRECT' );
}

# Give Bureaucrats delete permission
$wgGroupPermissions['bureaucrat']['delete'] = true;

# CookieExpiration in seconds
$wgCookieExpiration = 43200;
$wgExtendedLoginCookieExpiration = 43200;
