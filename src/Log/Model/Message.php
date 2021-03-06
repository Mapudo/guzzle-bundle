<?php
namespace Mapudo\Bundle\GuzzleBundle\Log\Model;

/**
 * Class Message
 *
 * @category Log Model
 * @package  Mapudo\Bundle\GuzzleBundle\Log\Model
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class Message
{
    /** @var string|null */
    protected $message;

    /** @var Request|null */
    protected $request;

    /** @var string|null */
    protected $client;

    /**
     * @return null|string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null|string $message
     * @return Message
     */
    public function setMessage(string $message = null): Message
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request|null $request
     * @return Message
     */
    public function setRequest(Request $request = null): Message
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param null|string $client
     * @return Message
     */
    public function setClient(string $client = null): Message
    {
        $this->client = $client;
        return $this;
    }
}
