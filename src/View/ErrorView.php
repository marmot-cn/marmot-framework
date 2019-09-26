<?php
namespace Marmot\Framework\View;

use Marmot\Interfaces\IView;
use Marmot\Framework\Classes\Server;
use Marmot\Core;

use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\EncoderOptions;
use Neomerx\JsonApi\Document\Link;
use Neomerx\JsonApi\Document\Error as JsonApiError;

/**
 * @codeCoverageIgnore
 */
class ErrorView implements IView
{
    public function display()
    {
        return $this->jsonApiError();
    }

    private function jsonApiError()
    {
        $lasetError = Core::getLastError();

        $error = new JsonApiError(
            $lasetError->getId(),
            new Link($lasetError->getLink()),
            $lasetError->getStatus(),
            $lasetError->getCode(),
            $lasetError->getTitle(),
            $lasetError->getDetail(),
            $lasetError->getSource(),
            $lasetError->getMeta()
        );

        return Encoder::instance(
            array(),
            new EncoderOptions(JSON_PRETTY_PRINT, Server::get('HTTP_HOST'))
        )->encodeError($error);
    }
}
