#!/bin/bash

set -e

# Create template for DPL extension.  If the page doesn't exist, the extension
# will redirect the fist login user to edit page.  That may break the LDAP
# authentication flow.  Hence we create the template here.  Not critical.  Allow failure.
: ${DPL_TEMPLATE_TITLE:='Template:Extension_DPL'}
php /var/www/html/maintenance/getText.php "$DPL_TEMPLATE_TITLE" > /dev/null 2>&1 || 
    (echo >&2 "Generating DPL template $DPL_TEMPLATE_TITLE" && 
     echo "<noinclude>This page was automatically created. It serves as an anchor page for all '''[[Special:WhatLinksHere/Template:Extension_DPL|invocations]]''' of [http://mediawiki.org/wiki/Extension:DynamicPageList Extension:DynamicPageList (DPL)].</noinclude>" | php /var/www/html/maintenance/edit.php -s "Template for DPL" "$DPL_TEMPLATE_TITLE" 
    ) || true
