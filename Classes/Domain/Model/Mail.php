<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Model;

class Mail extends \In2code\Powermail\Domain\Model\Mail
{
    /**
     * @var int
     */
    protected $plugin;

    /**
     * @return int
     */
    public function getPlugin(): int
    {
        return $this->plugin;
    }

    /**
     * @param int $plugin
     */
    public function setPlugin(int $plugin): void
    {
        $this->plugin = $plugin;
    }
}