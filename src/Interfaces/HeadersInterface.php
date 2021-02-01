<?php

declare(strict_types=1);

namespace Nymfonya\Component\Http\Interfaces;

interface HeadersInterface
{

    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_LENGTH = 'Content-Length';
    const ACCEPT_ENCODING = 'Accept-Encoding';
    const HEADER_ACA = 'Access-Control-Allow-';
    const HEADER_ACA_ORIGIN = self::HEADER_ACA . 'Origin';
    const HEADER_ACA_CREDENTIALS = self::HEADER_ACA . 'Credentials';
    const HEADER_ACA_METHODS = self::HEADER_ACA . 'Methods';
    const HEADER_ACA_HEADERS = self::HEADER_ACA . 'Headers';
    const REDIRECT_AUTHORIZATION = 'REDIRECT_HTTP_AUTHORIZATION';
    const AUTHORIZATION = 'Authorization';

    /**
     * add one header formaly done with given key and content
     *
     * @return HeadersInterface
     */
    public function add(string $key, string $content): HeadersInterface;

    /**
     * add multiples headers from assoc array and returns Headers instance
     *
     * @param array $headers
     * @return HeadersInterface
     */
    public function addMany(array $headers): HeadersInterface;

    /**
     * remove one header from his key and returns Headers instance
     *
     * @param string $key
     * @return HeadersInterface
     */
    public function remove(string $key): HeadersInterface;

    /**
     * returns all headers as assoc array
     *
     * @return array
     */
    public function get(): array;

    /**
     * returns all headers as normal array
     *
     * @return array
     */
    public function getRaw(): array;

    /**
     * send headers and returns Headers instance
     *
     * @return HeadersInterface
     */
    public function send(): HeadersInterface;
}
