<?php

if (!isset($_ENV['SIMPLESAMLPHP_IDP_METADATA_URL'])) {
    exit("Set env var SIMPLESAMLPHP_IDP_METADATA_URL to the IDP's metadata url");
}

$config = [
    'sets' => [
        'ubc' => [
            'cron' => ['startup'],
            'sources' => [
                ['src' => $_ENV['SIMPLESAMLPHP_IDP_METADATA_URL']]
            ],
            'expiresAfter' => 60*60*24*365*10, // 10 years, basically never
            'outputDir' => 'metadata/metarefresh-ubc/',
            'outputFormat' => 'flatfile',
        ]
    ]
];
