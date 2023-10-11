<?php

return [
    'powermail_cleaner' => [
        'path' => '/powermail/cleaner',
        'target' => \In2code\PowermailCleaner\Controller\ModuleController::class . '::cleanupAction',
    ],
];
