<?php
namespace Mapudo\Bundle\GuzzleBundle\Log\Model;

/**
 * Class Request
 *
 * @category Log Model
 * @package  Mapudo\Bundle\GuzzleBundle\Log\Model
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class Request
{
    /** @var string|null */
    protected $host;

    /** @var integer|null */
    protected $port;

    /** @var string|null */
    protected $url;

    /** @var string|null */
    protected $path;

    /** @var string|null */
    protected $scheme;

    /** @var array */
    protected $headers = [];

    /** @var string|null */
    protected $protocolVersion;

    /** @var string|null */
    protected $method;

    /** @var string|null */
    protected $body;

    /** @var string|null */
    protected $resource;

    /** @var Response|null */
    protected $response;

    /**
     * @return null|string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param null|string $host
     * @return Request
     */
    public function setHost(string $host = null): Request
    {
        $this->host = $host;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int|null $port
     * @return Request
     */
    public function setPort(int $port = null): Request
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param null|string $url
     * @return Request
     */
    public function setUrl(string $url = null): Request
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param null|string $path
     * @return Request
     */
    public function setPath(string $path = null): Request
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param null|string $scheme
     * @return Request
     */
    public function setScheme(string $scheme = null): Request
    {
        $this->scheme = $scheme;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @return Request
     */
    public function setHeaders(array $headers): Request
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param null|string $protocolVersion
     * @return Request
     */
    public function setProtocolVersion(string $protocolVersion = null): Request
    {
        $this->protocolVersion = $protocolVersion;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param null|string $method
     * @return Request
     */
    public function setMethod(string $method = null): Request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param null|string $body
     * @return Request
     */
    public function setBody(string $body = null): Request
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param null|string $resource
     * @return Request
     */
    public function setResource(string $resource = null): Request
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response|null $response
     * @return Request
     */
    public function setResponse(Response $response = null)
    {
        $this->response = $response;
        return $this;
    }
}
