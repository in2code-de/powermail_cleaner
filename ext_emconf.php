<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Powermail Cleaner',
    'description' => 'A Powermail extension to clean up answers with files via Scheduler / Command line or Backend',
    'category' => 'fe',
    'author' => 'Oliver Eglseder, Marcus Schwemer',
    'author_email' => 'oliver.eglseder@in2code.de, marcus.schwemer@in2code.de',
    'author_company' => 'in2code GmbH',
    'state' => 'stable',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.4.99',
            'powermail' => '12.0.0-12.9.99',
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
