<?php
namespace Docblocker\Console;

interface ProgressAwareInterface
{
    public function getProgress();

    public function isFinished();
}
