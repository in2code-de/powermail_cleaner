<?php
declare(strict_types=1);

namespace In2code\PowermailCleaner\Controller;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class FormController extends \In2code\Powermail\Controller\FormController {
    /**
     * @return ContentObjectRenderer
     */
    public function getContentObject() {
        return $this->contentObject;
    }
}
