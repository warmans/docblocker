<?php
namespace Docblocker\Console;

/**
 * Used by progress bars
 *
 * @package Docblocker\Console
 */
interface ProgressAwareInterface
{
    /**
     * @return int
     */
    public function getProgress();

    /**
     * @return bool
     */
    public function isFinished();
}
