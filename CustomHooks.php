<?php
use IMSGlobal\Caliper\entities\agent\Person;
use CaliperExtension\caliper\CaliperSensor;
use MediaWiki\MediaWikiServices;

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

            $userId = $user->getId();

            $dbr = MediaWikiServices::getInstance()
                ->getConnectionProvider()
                ->getReplicaDatabase();

            $puid = $dbr->newSelectQueryBuilder()
                ->select( 'puid' )
                ->from( $wgDBprefix . 'user_cwl_extended_account_data' )
                ->where( [
                    'user_id' => $userId,
                    'account_status' => 1,
                ] )
                ->caller( __METHOD__ )
                ->fetchField();

            if ( !$puid ) {
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
