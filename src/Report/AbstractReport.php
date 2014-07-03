<?php
namespace Docblocker\Report;

abstract class AbstractReport
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    abstract public function render();
}
