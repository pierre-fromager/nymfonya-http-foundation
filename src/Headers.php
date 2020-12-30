<?php

declare(strict_types=1);

namespace Nymfonya\Component\Http;

use Nymfonya\Component\Http\Interfaces\HeadersInterface;

class Headers implements HeadersInterface
{

    /**
     * headers list
     *
     * @var array
     */
    protected $headers;

    /**
     * instanciate
     *
     */
    public function __construct()
    {
        $this->headers = [];
    }

    /**
     * add one header to header list and returns Headers instance
     *
     * @param string $key
     * @param string $content
     * @return HeadersInterface
     */
    public function add(string $key, string $content): HeadersInterface
    {
        $this->headers[$key] = $content;
        return $this;
    }

    /**
     * add headers from a header list as key value
     * and returns Headers instance
     *
     * @param array $headers
     * @return HeadersInterface
     */
    public function addMany(array $headers): HeadersInterface
    {
        foreach ($headers as $k => $v) {
            $this->add($k, $v);
        }
        return $this;
    }

    /**
     * remove key header from header list
     * if header list key exists
     * and returns Headers instance
     *
     * @param string $key
     * @return HeadersInterface
     */
    public function remove(string $key): HeadersInterface
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }
        return $this;
    }

    /**
     * returns headers map as assoc array
     *
     * @return array
     */
    public function get(): array
    {
        return $this->headers;
    }

    /**
     * returns raw headers as a normal array
     *
     * @return array
     */
    public function getRaw(): array
    {
        return array_map(
            function ($key, $val) {
                return $key . ': ' . $val;
            },
            array_keys($this->headers),
            $this->headers
        );
    }

    /**
     * send headers and returns Headers instance
     *
     * @return HeadersInterface
     */
    public function send(): HeadersInterface
    {
        $headers = $this->getRaw();
        $headersCount = count($headers);
        for ($c = 0; $c < $headersCount; $c++) {
            header($headers[$c]);
        }
        unset($headersCount);
        unset($headers);
        return $this;
    }
}
