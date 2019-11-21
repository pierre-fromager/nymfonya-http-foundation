<?php

namespace Nymfonya\Component\Http\Interfaces;

use Nymfonya\Component\Http\Response;
use Nymfonya\Component\Http\Headers;
use Nymfonya\Component\Http\Interfaces\StatusInterface;

interface ResponseInterface extends StatusInterface
{

    const _CLI = 'cli';
    const _CLID = 'phpdbg';
    const _ERROR = 'error';
    const _ERROR_CODE = 'errorCode';
    const _ERROR_MSG = 'errorMessage';

    /**
     * returns header manager
     *
     * @return Headers
     */
    public function getHeaderManager(): Headers;

    /**
     * set response content
     *
     * @param mixed $content
     * @return Response
     */
    public function setContent($content): Response;

    /**
     * return content string
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * set http code response
     *
     * @param integer $code
     * @return Response
     */
    public function setCode(int $code): Response;

    /**
     * return http code response
     *
     * @return integer
     */
    public function getCode(): int;

    /**
     * send response content to output
     *
     * @return Response
     */
    public function send(): Response;
}
