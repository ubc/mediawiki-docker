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
