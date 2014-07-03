<?php
namespace Docblocker\Report;

class Json extends AbstractReport
{
    public function render()
    {
        return json_encode($this->getData());
    }
}
