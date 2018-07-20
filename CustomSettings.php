<?php
################
#  Namespaces  #
################
## Please rememver to define a constants for each namespace. Since
## SemanticMediaWiki uses the namespaces 100-109, the ids should
## start from 110 for our own custom namespaces. Even ids denotes a
## content NS, whereas odd ids denotes a duscussion NS (talk pages).

## Course Namepsace
define("NS_COURSE", 110);
define("NS_COURSE_TALK", 111);

$wgExtraNamespaces[NS_COURSE] = "Course";
$wgExtraNamespaces[NS_COURSE_TALK] = "Course_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_COURSE] = true;
$wgNamespacesWithSubpages[NS_COURSE_TALK] = true;

$wgContentNamespaces[] = NS_COURSE;


## Documentation Namespace
define("NS_DOCUMENTATION", 112);
define("NS_DOCUMENTATION_TALK", 113);

$wgExtraNamespaces[NS_DOCUMENTATION] = "Documentation";
$wgExtraNamespaces[NS_DOCUMENTATION_TALK] = "Documentation_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_DOCUMENTATION] = true;
$wgNamespacesWithSubpages[NS_DOCUMENTATION_TALK] = true;
$wgContentNamespaces[] = NS_DOCUMENTATION;


## Notepad Namespace
define("NS_NOTEPAD", 114);
define("NS_NOTEPAD_TALK", 115);

$wgExtraNamespaces[NS_NOTEPAD] = "Notepad";
$wgExtraNamespaces[NS_NOTEPAD_TALK] = "Notepad_talk";

$wgNamespacesToBeSearchedDefaulT[NS_NOTEPAD_TALK] = false;

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_NOTEPAD] = true;
$wgNamespacesWithSubpages[NS_NOTEPAD_TALK] = true;
$wgContentNamespaces[] = NS_NOTEPAD;


## YouTube Namespace
define("NS_YOUTUBE", 116);
define("NS_YOUTUBE_TALK", 117);

$wgExtraNamespaces[NS_YOUTUBE] = "YouTube";
$wgExtraNamespaces[NS_YOUTUBE_TALK] = "YouTube_talk";


## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_YOUTUBE] = false;
$wgNamespacesWithSubpages[NS_YOUTUBE_TALK] = false;
$wgContentNamespaces[] = NS_YOUTUBE;

## Elearning Namespace
define("NS_ELEARNING", 118);
define("NS_ELEARNING_TALK", 119);

$wgExtraNamespaces[NS_ELEARNING] = "Elearning";
$wgExtraNamespaces[NS_ELEARNING_TALK] = "Elearning_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_ELEARNING] = true;
$wgNamespacesWithSubpages[NS_ELEARNING_TALK] = false;

$wgContentNamespaces[] = NS_ELEARNING;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_ELEARNING] = array('elearning');

## Elearning User Group ##
// this group can can edit elearning
$wgGroupPermissions['elearning']['elearning'] = true;

$wgNamespacesWithSubpages[NS_HELP] = true;
$wgNamespacesWithSubpages[NS_HELP] = true;

## Library Namespace
define("NS_LIBRARY", 120);
define("NS_LIBRARY_TALK", 121);

$wgExtraNamespaces[NS_LIBRARY] = "Library";
$wgExtraNamespaces[NS_LIBRARY_TALK] = "Library_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LIBRARY] = true;
$wgNamespacesWithSubpages[NS_LIBRARY_TALK] = false;

$wgContentNamespaces[] = NS_LIBRARY;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_LIBRARY] = array('library');

## Library User Group ##
// this group can can edit Library
$wgGroupPermissions['library']['library'] = true;
$wgGroupPermissions['library']['reupload'] = true;
## Library Manager Group ##
## This group can add users to the Library Group ##
// this group can can edit Library
$wgGroupPermissions['library_manager']['library'] = true;
// Library Manager can add users to library and linbrary_manager
$wgAddGroups['library_manager'] = array('library','library_manager');
// Library Manager can add users
$wgRemoveGroups['library_manager'] = array('library','library_manager');

## Notepad SANDBOX
define("NS_SANDBOX", 122);
define("NS_SANDBOX_TALK", 123);

$wgExtraNamespaces[NS_SANDBOX] = "Sandbox";
$wgExtraNamespaces[NS_SANDBOX_TALK] = "Sandbox_talk";

$wgNamespacesToBeSearchedDefaulT[NS_SANDBOX_TALK] = false;

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_SANDBOX] = true;
$wgNamespacesWithSubpages[NS_SANDBOX_TALK] = true;

$wgContentNamespaces[] = NS_SANDBOX;

## Arts Group / Namespace ##
define("NS_ARTS", 124);
define("NS_ARTS_TALK", 125);

$wgExtraNamespaces[NS_ARTS] = "Arts";
$wgExtraNamespaces[NS_ARTS_TALK] = "Arts_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_ARTS] = true;
$wgNamespacesWithSubpages[NS_ARTS_TALK] = false;

$wgContentNamespaces[] = NS_ARTS;

// Only Arts  Group can edit.
$wgNamespaceProtection[NS_ARTS] = array('arts');

## Arts User Group ##
// this group can can edit Arts
$wgGroupPermissions['arts']['arts'] = true;

## Learning Commons Group / Namespace ##
define("NS_LEARNINGCOMMONS", 126);
define("NS_LEARNINGCOMMONS_TALK", 127);

$wgExtraNamespaces[NS_LEARNINGCOMMONS] = "Learning_Commons";
$wgExtraNamespaces[NS_LEARNINGCOMMONS_TALK] = "Learning_Commons_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LEARNINGCOMMONS] = true;
$wgNamespacesWithSubpages[NS_LEARNINGCOMMONS_TALK] = false;

$wgContentNamespaces[] = NS_LEARNINGCOMMONS;

## LEARNING COMMONS User Group ##
// this group can can edit Learning Commons
$wgGroupPermissions['learning_commons']['learning_commons'] = true;

// Only LEARNING COMMONS Group can edit.
//$wgNamespaceProtection[NS_LEARNINGCOMMONS] = array('learning_commons');

## LFS Land and Food System Namespace
define("NS_LFS", 128);
define("NS_LFS_TALK", 129);

$wgExtraNamespaces[NS_LFS] = "LFS";
$wgExtraNamespaces[NS_LFS_TALK] = "LFS_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_LFS] = true;
$wgNamespacesWithSubpages[NS_LFS_TALK] = false;

$wgContentNamespaces[] = NS_LFS;

// Only LFS Group can edit.
$wgNamespaceProtection[NS_LFS] = array('lfs');

## LFS User Group ##
// this group can can edit LFS
$wgGroupPermissions['lfs']['lfs'] = true;

## LFS Manager Group ##
## This group can add users to the LFS Group ##
// this group can can edit LFS
$wgGroupPermissions['lfs_manager']['lfs'] = true;
// LFS Manager can add users to lfs and lfs_manager
$wgAddGroups['lfs_manager'] = array('lfs','lfs_manager');
// LFS Manager can add users
$wgRemoveGroups['lfs_manager'] = array('lfs','lfs_manager');

## END LFS Land and Food System Namespace

## Science Namespace
define("NS_SCIENCE", 130);
define("NS_SCIENCE_TALK", 131);

$wgExtraNamespaces[NS_SCIENCE] = "Science";
$wgExtraNamespaces[NS_SCIENCE_TALK] = "Science_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_SCIENCE] = true;
$wgNamespacesWithSubpages[NS_SCIENCE_TALK] = false;

$wgContentNamespaces[] = NS_SCIENCE;

// Only Elearning Group can edit.
$wgNamespaceProtection[NS_SCIENCE] = array('science');

## Science User Group ##
// this group can can edit Library
$wgGroupPermissions['science']['science'] = true;

## Science Manager Group ##
## This group can add users to the Science Group ##
// this group can can edit Science
$wgGroupPermissions['science_manager']['science'] = true;
// Science Manager can add users to science and science_manager
$wgAddGroups['science_manager'] = array('science','science_manager');
// Science Manager can add users
$wgRemoveGroups['science_manager'] = array('science','science_manager');

# Define the THREAD NAMESPACE
define("NS_THREAD", 90);
define("NS_THREAD_TALK", 91);

# Define the SUMMARY  NAMESPACE
define("NS_SUMMARY", 92);
define("NS_SUMMARY_TALK", 93);

## Namespace COPYRIGHT
define("NS_COPYRIGHT", 140);
define("NS_COPYRIGHT_TALK", 141);

$wgExtraNamespaces[NS_COPYRIGHT] = "Copyright";
$wgExtraNamespaces[NS_COPYRIGHT_TALK] = "Copyright_talk";

## Subpages has to be enabled explictly.
$wgNamespacesWithSubpages[NS_COPYRIGHT] = true;
$wgNamespacesWithSubpages[NS_COPYRIGHT_TALK] = false;

$wgContentNamespaces[] = NS_COPYRIGHT;

// Only Copyright Group can edit.
$wgNamespaceProtection[NS_COPYRIGHT] = array('copyright');

## Copyright User Group ##
// this group can can edit Library
$wgGroupPermissions['copyright']['copyright'] = true;

## Cpyright Manager Group ##
## This group can add users to the Copyright Group ##
// this group can can edit Copyright
$wgGroupPermissions['copyright_manager']['copyright'] = true;
//  Copyright Manager can add users to copyright and copyright_manager
$wgAddGroups['copyright_manager'] = array('copyright','copyright_manager');
// Copyright Manager can add users
$wgRemoveGroups['copyright_manager'] = array('copyright','copyright_manager');

## Search these Namespances by default
$wgNamespacesToBeSearchedDefault = array(
        NS_MAIN           => true,
        NS_USER           => true,
        NS_HELP           => true,
        NS_COURSE         => true,
        NS_COURSE_TALK    => true,
        NS_THREAD         => true,
        NS_SUMMARY        => true,
        NS_DOCUMENTATION  => true,
        NS_FILE           => true,
        NS_CATEGORY       => true,
        NS_YOUTUBE        => true,
        NS_ELEARNING      => true,
        NS_LIBRARY        => true,
        NS_NOTEPAD        => true,
        NS_ARTS           => true,
        NS_LEARNINGCOMMONS => true,
        NS_LFS             => true,
        NS_SCIENCE         => true,
        NS_COURSE          => true,
);
#####################
## End Namepsace Area
#####################


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

        if (empty($info)) {
            /*
            Sometimes wiki will call this hook without giving us the LDAP info.
            It will cause problem if memcached is enabled.  So we stored a copy
            of previously translated username in session and return it here.
            */
            if (array_key_exists('ldap_wiki_username', $_SESSION)) {
                $LDAPUsername = $_SESSION['ldap_wiki_username'];
            }
            return true;
        }

        $puidFromLDAP = _puid_from_ldap($info);
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
        $_SESSION['ldap_wiki_username'] = $LDAPUsername;
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
        return _ldap_get_or_empty($info, 'uid');
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
} // end customization for LDAP authentication