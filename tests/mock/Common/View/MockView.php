<?php
namespace Marmot\Framework\Common\View;

use Marmot\Interfaces\IView;

use Marmot\Framework\View\JsonApiTrait;

class MockView implements IView
{
    use JsonApiTrait;

    private $rules;

    private $data;
    
    private $encodingParameters;

    public function __construct($data, $encodingParameters = null)
    {
        $this->data = $data;
        $this->encodingParameters = $encodingParameters;
        $this->rules = array();
    }
    
    public function display()
    {
        return $this->jsonApiFormat($this->data, $this->rules, $this->encodingParameters);
    }
}
