<?php

return [
    'powermail_cleaner' => [
        'parent' => 'web_powermail',
        'position' => ['after' => 'checkBe'],
        'access' => 'admin',
        'iconIdentifier' => 'extension-powermail-main',
        'labels' => [
            'title' => 'Powermail Cleaner',
        ],
        'path' => '/module/powermail/cleaner',
        'target' => [
            '_default' => \In2code\PowermailCleaner\Controller\ModuleController::class . '::cleanupAction',
        ],
        'extensionName' => 'PowermailCleaner',
        'controllerActions' => [
            \In2code\PowermailCleaner\Controller\ModuleController::class =>
                'cleanup',
        ],
    ],
];
