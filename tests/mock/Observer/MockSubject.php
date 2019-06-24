<?php
namespace Marmot\Framework\Observer;

class MockSubject extends Subject
{
    public function getObservers() : array
    {
        return parent::getObservers();
    }
}
