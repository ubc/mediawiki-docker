<?php
use Exception;

use IMSGlobal\Caliper\entities\agent\Person;
use CaliperExtension\caliper\CaliperSensor;

# If LDAP environment variables are defined, enable additional customization
if (getenv('LDAP_SERVER') || getenv('LDAP_BASE_DN') || getenv('LDAP_SEARCH_STRINGS') || getenv('LDAP_SEARCH_ATTRS')) {

    // Remove the change password link from Preferences page.
    // ref: https://stackoverflow.com/questions/16893589/prevent-users-from-changing-their-passwords-in-mediawiki
    // note: many of the hooks mentioned in the stackoverflow post above have been deprecated
    $wgHooks['GetPreferences'][] = 'RemovePasswordChangeLink';
    function RemovePasswordChangeLink($user, &$preferences) {
        unset($preferences['password']);
        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////

    $wgHooks['AuthChangeFormFields'][] = 'ChangeAuthFormFields';
    function ChangeAuthFormFields($requests, $fieldInfo, &$formDescriptor, $action) {
        global $wgCookiePrefix;

        // Remove "local" domain option from login page
        unset($formDescriptor['domain']['options']['local']);

        // Remove username from cookies to avoid prefilling the field with wiki username.
        // Users should authenticate with usernames in LDAP.
        unset($_COOKIE[$wgCookiePrefix.'UserName']);

        return true;
     }

    # if Caliper is setup, use a custom actor with puid from LDAP
    if (getenv('CALIPER_HOST') && getenv('CALIPER_API_KEY')) {
        $wgHooks['SetCaliperActorObject'][] = 'SetCaliperActor';

        // This is the username MediaWiki will use.
        function SetCaliperActor(&$actor, &$user) {
            global $wgDBprefix;

            if ($actor !== null) {
                return true;
            } else if (!$user->isRegistered() || !$user->getId()) {
                return false;
            }

            $puid = null;
            $userId = $user->getId();

            $dbr = wfGetDB(DB_REPLICA);
            $res = $dbr->select(
                array('ucead' => $wgDBprefix.'user_cwl_extended_account_data'),   // tables
                array('ucead.puid'),       // fields
                array('ucead.user_id' => $userId, 'ucead.account_status' => 1),   // where clause
                __METHOD__,     // caller function name
                array('LIMIT' => 1)      // options. fetch first row only
            );
            foreach ($res as $row) {
                $puid = $row->puid;
            }

            if (!$puid) {
                return false;
            }

            $caliperLDAPActorHomepage = rtrim(loadenv('CALIPER_LDAP_ACTOR_HOMEPAGE', ''), '/');

            $actor = (new Person( $caliperLDAPActorHomepage . "/" . $puid ))
                ->setName($user->getName())
                ->setDateCreated(CaliperSensor::mediawikiTimestampToDateTime($user->getRegistration()));

            return true;
        }
    }
} // end customization for LDAP authentication

#####################
## End LDAP customization
#####################
