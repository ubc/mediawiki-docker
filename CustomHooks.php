<?php

use IMSGlobal\Caliper\entities\agent\Person;
use CaliperExtension\caliper\CaliperSensor;

# If LDAP environment variables are defined, enabled additional customization
if (getenv('LDAP_SERVER') || getenv('LDAP_BASE_DN') || getenv('LDAP_SEARCH_STRINGS') || getenv('LDAP_SEARCH_ATTRS')) {
    // $wgDebugLogFile = "/tmp/debug-{$wgDBname}.log";
    // $wgDebugDumpSql = true;

    ///////////////////////////////////////////////////////////////////////////////

    // link cwl login to wiki user
    $wgHooks['SetUsernameAttributeFromLDAP'][] = 'SetUsernameAttribute';

    // This is the username MediaWiki will use.
    function SetUsernameAttribute(&$LDAPUsername, $info) {
        global $wgDBprefix, $wgServer;

        $puidFromLDAP = _puid_from_ldap($info);
        if (empty($puidFromLDAP)) {
            $LDAPUsername = '';
            return true;
        }

        $LDAPUsername = _cwl_login_from_ldap($info);  // default wiki username

        $existing_user_found = false;

        // Change the username if found matching record in db with puid.
        if ($puidFromLDAP) {
            $dbr = wfGetDB(DB_REPLICA);
            $res = $dbr->select(
                array('ucead' => $wgDBprefix.'user_cwl_extended_account_data', 'u' => $wgDBprefix.'user'),   // tables
                array('u.user_name'),       // fields
                array('ucead.puid' => $puidFromLDAP, 'ucead.account_status' => 1),   // where clause
                __METHOD__,     // caller function name
                array('LIMIT' => 1),      // options. fetch first row only
                array('u' => array('INNER JOIN', array(     // join the tables
                    'ucead.user_id = u.user_id'
                )))
            );
            foreach ($res as $row) {
                $LDAPUsername = $row->user_name;
                $existing_user_found = true;
            }
            $dbr->freeResult($res);
        }

        // if no matching wiki account found, create one and link with cwl login
        if (!$existing_user_found) {
            // create new wiki user and insert record into cwl extended data table
            $username = _generate_new_wiki_username($info);
            $first_name = _first_name_from_ldap($info);
            $last_name = _last_name_from_ldap($info);
            $email = _email_from_ldap($info);
            $puid = _puid_from_ldap($info);
            $cwl_login_name = _cwl_login_from_ldap($info);
            $ubcAffiliation = '';   // TODO still needed? where to get it from LDAP?

            try{
                $new_user_id = _create_wiki_user($username, $first_name, $last_name, $email);
                if (empty($new_user_id)) {
                    throw new Exception('Failed to create new wiki user');
                }
                if (!_create_cwl_extended_account_data($new_user_id, $puid, $cwl_login_name, $ubcAffiliation, $first_name, $last_name)) {
                    throw new Exception('Failed to create CWL extended data record');
                }

                $LDAPUsername = $username;
            } catch (Exception $e) {
                // failed to create new user
                wfDebugLog('error', $e->getTraceAsString());
                throw new MWException('Failed to create new wiki user');
            }
        }

        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////

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

    ///////////////////////////////////////////////////////////////////////////////
    /*
    There is an issue with LDAP login if we also use SetUsernameAttributeFromLDAP
    to modify username. If the login failed (e.g. incorrect password),
    subsequent logins will fail even with correct credential. Users could only login
    again by clearing browser cookies.
    This is a hack to get around it by clearing the session data on backend if login failed.
    */
    $wgHooks['AuthManagerLoginAuthenticateAudit'][] = 'onAuthManagerLoginAuthenticateAudit';
    function onAuthManagerLoginAuthenticateAudit($response, $user, $username) {
        if ($response && $response->status === "FAIL") {
            session_destroy();
        }
    }

    ///////////////////////////////////////////////////////////////////////////////
    /*
    When renaming a user, clear the canonical name cached by LDAP auth
    */
    $wgHooks['RenameUserAbort'][] = 'onRenameUserAbort';
    function onRenameUserAbort($uid, $oldName, $newName) {
        global $wgMemc, $wgDBprefix;
        global $wgLDAPLowerCaseUsername;

        // find our record based on uid
        $dbr = wfGetDB(DB_REPLICA);
        $res = $dbr->select(
            array('ucead' => $wgDBprefix.'user_cwl_extended_account_data'),   // tables
            array('ucead.cwllogin'),       // fields
            array('ucead.user_id' => $uid),   // where clause
            __METHOD__     // caller function name
        );
        foreach ($res as $row) {
            $username = $row->cwllogin;
            if ($wgLDAPLowerCaseUsername) {
                $username = strtolower($username);
            }
            $key = wfMemcKey('ldapauthentication', 'canonicalname', ucfirst($username));
            $wgMemc->delete($key);
        }
        $dbr->freeResult($res);

        return true;
    }

    ///////////////////////////////////////////////////////////////////////////////
    // helper functions

    function _ldap_get_or_empty($info, $key) {
        if ($info && array_key_exists(0, $info) &&
            array_key_exists($key, $info[0]) &&
            array_key_exists(0, $info[0][$key])) {
            return $info[0][$key][0];
        }
        return '';
    }

    // user information from LDAP
    function _cwl_login_from_ldap($info) {
        return _ldap_get_or_empty($info, getenv('LDAP_SEARCH_ATTRS')? getenv('LDAP_SEARCH_ATTRS') : 'uid');
    }
    function _first_name_from_ldap($info) {
        return _ldap_get_or_empty($info, 'givenname');
    }
    function _last_name_from_ldap($info) {
        return _ldap_get_or_empty($info, 'sn');
    }
    function _puid_from_ldap($info) {
        return _ldap_get_or_empty($info, 'ubceducwlpuid');
    }
    function _email_from_ldap($info) {
        return _ldap_get_or_empty($info, 'mail');
    }

    // create a new wiki user in DB and return the new user id
    function _create_wiki_user($username, $first_name, $last_name, $email) {
        $u = User::newFromId(NULL);
        $u->setName($username);
        $u->addToDatabase();
        $u->setEmail($email);
        $u->setRealName($first_name . " " . $last_name);
        $u->setToken();
        // leave the password as empty to prevent login with local wiki user
        $u->saveSettings();
        return $u->getID();
    }

    /**
     * _create_cwl_extended_account_data  - insert new record to cwl_extended_account_data
     *
     * @param string $user_id Mediawiki user_id
     * @param string $puid user PUID
     * @param string $cwlLoginName
     * @param string $ubcAffiliation
     * @param string $first_name
     * @param string $last_name
     * @return bool
     */
    function _create_cwl_extended_account_data($user_id, $puid, $cwlLoginName, $ubcAffiliation, $first_name, $last_name) {
        global $wgDBprefix;

        $dbw = wfGetDB(DB_MASTER);
        $table = $wgDBprefix."user_cwl_extended_account_data";

        $ubcAffiliation = preg_replace("/[^A-Za-z0-9 ]/", '', $ubcAffiliation);
        $full_name = preg_replace("/[^A-Za-z0-9 ]/", '', $first_name . ' ' . $last_name);

        $insert_a = array(
            'user_id' => $user_id,
            'puid'    => $puid,
            'ubc_role_id' => '',  // no longer captured doing SSO
            'ubc_dept_id' => '', // no longer captured doing SSO
            'wgDBprefix' => $wgDBprefix,
            'CWLLogin' => $cwlLoginName,
            'CWLRole' => $ubcAffiliation,   // TODO: check if this field is used
            'CWLNickname' => $full_name,
            //'CWLSaltedID' => $CWLSaltedID, // no longer needed using PUID
            'account_status' => 1   //might never be used.
        );

        $res_ad = $dbw->insert($table, $insert_a);
        return $res_ad;
    }

    // check if given wiki username exist
    function _wiki_user_exist($username) {
        global $wgDBprefix;

        $found = false;
        $dbr = wfGetDB(DB_REPLICA);
        $res = $dbr->select(
            array('u' => $wgDBprefix.'user'),   // tables
            array('u.user_name'),       // fields
            array('u.user_name' => $username),   // where clause
            __METHOD__,     // caller function name
            array('LIMIT' => 1)      // options. fetch first row only
        );
        foreach ($res as $row) {
            $found = true;
        }
        $dbr->freeResult($res);
        return $found;
    }

    // generate a new and unique wiki user name based on LDAP data
    function _generate_new_wiki_username($info) {
        // similar logic as existing CASAuth
        $first_name = _first_name_from_ldap($info);
        $last_name = _last_name_from_ldap($info);
        $ucfirst_name = ucfirst(preg_replace("/[^A-Za-z0-9]/", '', $first_name));
        $uclast_name  = ucfirst(preg_replace("/[^A-Za-z0-9]/", '', $last_name));
        $username = $ucfirst_name.$uclast_name;
        if (empty($username)) {
            // use cwl login if name is empty
            return _cwl_login_from_ldap($info);
        }

        $num = 1;
        while (_wiki_user_exist($username)) {
            $username = $ucfirst_name.$uclast_name.$num;
            if ($num++ > 9999) {
                // avoid infinite loop
                return _cwl_login_from_ldap($info);
            }
        }
        return $username;
    }

    # if Caliper is setup, use a custom actor with puid from LDAP
    if (getenv('CaliperHost') && getenv('CaliperAPIKey')) {
        $wgHooks['SetCaliperActorObject'][] = 'SetCaliperActor';

        // This is the username MediaWiki will use.
        function SetCaliperActor(&$actor, &$user) {
            global $wgDBprefix;

            if ($actor !== null) {
                return true;
            } else if (!$user->isLoggedIn() || !$user->getId()) {
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

            $caliperLDAPActorHomepage = rtrim(loadenv('CaliperLDAPActorHomepage', ''), '/');

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

