<?php

namespace Mapudo\Bundle\GuzzleBundle\Log\Model;

/**
 * Class Response
 *
 * @category Log Model
 * @package  Mapudo\Bundle\GuzzleBundle\Log
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class Response
{
    /** @var integer|null */
    protected $statusCode;

    /** @var string|null */
    protected $reasonPhrase;

    /** @var string|null */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string|null */
    protected $protocolVersion;

    /**
     * Return HTTP status code
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set HTTP status code
     *
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode(int $statusCode): Response
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Return HTTP status phrase
     * @return string
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * Set HTTP status phrase
     *
     * @param string $reasonPhrase
     * @return Response
     */
    public function setReasonPhrase(string $reasonPhrase): Response
    {
        $this->reasonPhrase = $reasonPhrase;
        return $this;
    }

    /**
     * Return response body
     * @return string|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set response body
     *
     * @param string|null $body
     * @return Response
     */
    public function setBody(string $body = null): Response
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Return protocol version
     * @return string|null
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Set protocol version
     *
     * @param string $protocolVersion
     * @return Response
     */
    public function setProtocolVersion(string $protocolVersion): Response
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    /**
     * Return response headers
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set response headers
     *
     * @param array $headers
     * @return Response
     */
    public function setHeaders(array $headers): Response
    {
        $this->headers = $headers;
        return $this;
    }
}
