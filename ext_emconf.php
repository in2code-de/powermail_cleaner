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
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
            'powermail' => '10.7.0-10.7.99',
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
