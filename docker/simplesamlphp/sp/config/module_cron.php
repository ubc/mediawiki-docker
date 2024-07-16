<?php

if (!isset($_ENV['SIMPLESAMLPHP_CRON_SECRET'])) {
    exit("Set env var SIMPLESAMLPHP_CRON_SECRET to a random alphanumeric string");
}

$config = [
    'key' => $_ENV['SIMPLESAMLPHP_CRON_SECRET'],
    'allowed_tags' => ['startup'],
    'debug_message' => true,
    'sendemail' => false,
];
