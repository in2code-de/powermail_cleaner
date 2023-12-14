<?php

declare(strict_types=1);

namespace In2code\PowermailCleaner\Domain\Model;

class Mail extends \In2code\Powermail\Domain\Model\Mail
{
    /**
     * @var int
     */
    protected $plugin;

    protected int $deletionTimestamp = 0;

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

    public function getDeletionTimestamp(): int
    {
        return $this->deletionTimestamp;
    }

    public function setDeletionTimestamp(int $deletionDate): void
    {
        $this->deletionTimestamp = $deletionDate;
    }
}