<?php

$metadata['http://wiki.docker:8080/_saml2'] = [
    'SingleLogoutService' => [
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
            'Location' => 'http://wiki.docker:8080/_saml2/module.php/saml/sp/saml2-logout.php/wiki-sp',
        ],
    ],
    'AssertionConsumerService' => [
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'http://wiki.docker:8080/_saml2/module.php/saml/sp/saml2-acs.php/wiki-sp',
            'index' => 0,
        ],
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Artifact',
            'Location' => 'http://wiki.docker:8080/_saml2/module.php/saml/sp/saml2-acs.php/wiki-sp',
            'index' => 1,
        ],
    ],
    'contacts' => [
        [
            'emailAddress' => 'lt.hub@ubc.ca',
            'givenName' => 'UBC LT Hub',
            'contactType' => 'technical',
        ],
    ],
    'certData' => 'MIIEcTCCAtmgAwIBAgIUWmBx+tf9d7hKrFe9sjuhClKXFZ8wDQYJKoZIhvcNAQELBQAwSDELMAkGA1UEBhMCQ0ExEjAQBgNVBAcMCVZhbmNvdXZlcjEMMAoGA1UECgwDVUJDMRcwFQYDVQQDDA5zcC53aWtpLmRvY2tlcjAeFw0yNDA3MDQwOTA4MzZaFw0zNDA3MDQwOTA4MzZaMEgxCzAJBgNVBAYTAkNBMRIwEAYDVQQHDAlWYW5jb3V2ZXIxDDAKBgNVBAoMA1VCQzEXMBUGA1UEAwwOc3Aud2lraS5kb2NrZXIwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQDCEa0f5ZJhpSU+Xc0WNohbxTzpmDkqgI0rtWCmL5vqJakPCHnWnq0icCX2/zwh6//WP+9UPgO1ifHUhNC/NEJhBKGJjtNNKaV+AwUzj43IiLMqgkhMEvkqNePuKNBh/lvzjLl3KYMrLAEZKx+AluMaS7us5CmR9lyhY9nHZS0P1FRjwJ6SJ1o0HEuXHkH5eRotaRtrd8L+L93R9SaIBpgAy0XMkgFDqGmX7NbVAMT6cPNEVmj63J5veMtpCN5mQRXpZFPCSbmXOGlyy7S3cilpSk8QA8QOkt4EB+I6G5W/aaG8hNs4QHKkKMReJ/oHQbQXIJ2d4oMsQaEXk3FtTIbl4l7fKS+LvhCHvB9z8q/ueh3bAIcpSxGzg3oTScZM5ZZAqzjYxCMYdI+3h44FPUtDsZdwezFN/B+JsITQouaYzuRxjUV6uNGhZXSRb+st3VYIBg0+mIvowDyBHgQvOaAZ8/UuSqcfrMH/AwTVY2Ej2YzerKDCwchHmpv5sXRY+o8CAwEAAaNTMFEwHQYDVR0OBBYEFIUt4n/0ouPzNfRNonY/EtJhHXPfMB8GA1UdIwQYMBaAFIUt4n/0ouPzNfRNonY/EtJhHXPfMA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcNAQELBQADggGBAAK5QNOmFjLmQZdfWURK+hyCN08RIB6qOgKxuMG6j6u4brKOhktRAx+8hwrgVH96+fW3DkELsNGTTjUzxJvXM01cDDn2lUNMhLA2InHTsFe2zbmKG5sSl0wOFhi0kBnkGL8di3FgnqJJs8sTcQWajoFiEPa0yW3Gad/S6JSPgrHMlPkMPgZ8Vw8aYVprronbj9eiGWzRO5vFrE6YMn2l9es/pVJKzsb362EPhFekJA6f+6Ek2rfPRd0KiF5+Pln8KSooRmXpOZkM2CUfgOmb3lT9mwel2wemnXjUj0sjN5luotbK6YVhnwuq9d1O1a8Lhx8HLLasV7bR1hg9rjz+K2nv1XqWYsiFJelkgD4DOcFP68I/eiUiAf6jqh5+YJuqFXkXS9P6ohOXn5sbiV69+VV64JXG31emPgX/mm/41Bq2j5ESYak1I4RCPdLPpsjPWUMUKAXrRjbj8UZBf5w3Uv7tc4SY+Sc8mcBw0/14Ossz5h2ZLBW0j1QKqDWwSyWn5A==',
];
