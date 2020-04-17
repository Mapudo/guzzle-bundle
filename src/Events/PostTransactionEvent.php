<?php
namespace Mapudo\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class PreTransactionEvent
 *
 * @category Event
 * @package  Mapudo\Bundle\GuzzleBundle\Events
 * @link     http://www.mapudo.com
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 */
class PostTransactionEvent extends Event
{
    /** @var ResponseInterface */
    protected $responseTransaction;

    /** @var string */
    protected $clientName;

    /**
     * PostTransactionEvent constructor.
     *
     * @param ResponseInterface $responseTransaction
     * @param string            $clientName
     */
    public function __construct(ResponseInterface $responseTransaction, string $clientName)
    {
        $this->responseTransaction = $responseTransaction;
        $this->clientName = $clientName;
    }

    /**
     * Return the transaction
     * @return ResponseInterface
     */
    public function getTransaction(): ResponseInterface
    {
        return $this->responseTransaction;
    }

    /**
     * Set the transaction to a response
     *
     * @param ResponseInterface $response
     * @return PostTransactionEvent
     */
    public function setTransaction(ResponseInterface $response): PostTransactionEvent
    {
        $this->responseTransaction = $response;
        return $this;
    }

    /**
     * Returns the name of the client for this event
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }
}
