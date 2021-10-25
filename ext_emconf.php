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
    'version' => '1.0.4',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.99.99',
            'powermail' => '6.2.0-7.4.99',
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
