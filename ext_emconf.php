<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Powermail Cleaner',
    'description' => 'A Powermail extension to clean up answers with files via Scheduler / Command line or Backend',
    'category' => 'fe',
    'author' => 'Oliver Eglseder',
    'author_email' => 'oliver.eglseder@in2code.de',
    'author_company' => 'in2code GmbH',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.1.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'powermail' => '8.0.0-8.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'In2code\\PowermailCleaner\\' => 'Classes'
        ]
    ]
];
