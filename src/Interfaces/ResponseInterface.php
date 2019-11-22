<?php

namespace Nymfonya\Component\Http\Interfaces;

use Nymfonya\Component\Http\Interfaces\HeadersInterface;
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
     * @return HeadersInterface
     */
    public function getHeaderManager(): HeadersInterface;

    /**
     * set response content
     *
     * @param mixed $content
     * @return ResponseInterface
     */
    public function setContent($content): ResponseInterface;

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
     * @return ResponseInterface
     */
    public function setCode(int $code): ResponseInterface;

    /**
     * return http code response
     *
     * @return integer
     */
    public function getCode(): int;

    /**
     * send response content to output
     *
     * @return ResponseInterface
     */
    public function send(): ResponseInterface;
}
