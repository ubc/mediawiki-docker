<?php
use Exception;

use IMSGlobal\Caliper\entities\agent\Person;
use CaliperExtension\caliper\CaliperSensor;

if (filter_var( getenv( 'UBC_AUTH_ENABLED' ), FILTER_VALIDATE_BOOLEAN )) {
    # if Caliper is setup, use a custom actor with puid
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
}
