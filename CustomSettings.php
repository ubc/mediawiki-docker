<?php

# some sensible defaults

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


# WikiEditor
# ref: https://www.mediawiki.org/wiki/Extension:WikiEditor

# Enables use of WikiEditor by default but still allows users to disable it in preferences
$wgDefaultUserOptions['usebetatoolbar'] = 1;

# Enables link and table wizards by default but still allows users to disable them in preferences
$wgDefaultUserOptions['usebetatoolbar-cgd'] = 1;

# RESTBase
# ref: https://www.mediawiki.org/wiki/Extension:VisualEditor

$wgVirtualRestConfig['modules']['restbase'] = [
  'url' => getenv('RESTBASE_URL') ? getenv('RESTBASE_URL') : 'http://localhost:7231',
  'domain' => getenv('PARSOID_DOMAIN') ? getenv('PARSOID_DOMAIN') : 'localhost',
  'parsoidCompat' => false
];

# If Ldap environment variables are defined, enabled ldap function
if (getenv('LDAP_SERVER') || getenv('LDAP_BASE_DN') || getenv('LDAP_SEARCH_STRINGS') || getenv('LDAP_SEARCH_ATTRS')) {
    require_once ("$IP/extensions/LdapAuthentication/LdapAuthentication.php");
    $wgAuth = new LdapAuthenticationPlugin();

    $wgLDAPUseLocal = getenv('LDAP_USE_LOCAL') ? getenv('LDAP_USE_LOCAL') == 'true' : true;

    $wgLDAPDebug = getenv('LDAP_DEBUG') ? getenv('LDAP_DEBUG') : 0;
    $wgDebugLogGroups['ldap'] = '/tmp/debug.log';

    $ldapDomain = getenv('LDAP_DOMAIN') ? getenv('LDAP_DOMAIN') : 'LOCAL';
    $wgLDAPDomainNames       = array($ldapDomain);
    $wgLDAPServerNames       = array($ldapDomain => getenv('LDAP_SERVER') ? getenv('LDAP_SERVER') : 'localhost');
    $wgLDAPEncryptionType    = array($ldapDomain => 'clear');
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
}


# Scribunto
require_once "$IP/extensions/Scribunto/Scribunto.php";
$wgScribuntoDefaultEngine = 'luastandalone';
