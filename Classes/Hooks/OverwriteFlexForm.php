<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Hooks;

/**
 * Class OverwriteFlexForm
 */
class OverwriteFlexForm
{
    /**
     * @var string
     */
    protected $path = 'FILE:EXT:powermail_cleaner/Configuration/FlexForms/FlexformPi1.xml';

    /**
     * @return void
     */
    public function overwrite()
    {
        $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']['powermail_pi1,list']
            = $this->path;
    }
}