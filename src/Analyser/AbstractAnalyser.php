<?php
namespace Docblocker\Analyser;

abstract class AbstractAnalyser
{
    protected $rawData;

    public function __construct(array &$rawData)
    {
        $this->rawData =& $rawData;
    }

    abstract public function analyse();
}
