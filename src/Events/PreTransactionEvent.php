<?php
namespace Mapudo\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\RequestInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class PreTransactionEvent
 *
 * @category Event
 * @package  Mapudo\Bundle\GuzzleBundle\Events
 * @link     http://www.mapudo.com
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 */
class PreTransactionEvent extends Event
{
    /** @var RequestInterface */
    protected $requestTransaction;

    /** @var string */
    protected $clientName;

    /**
     * PostTransactionEvent constructor.
     *
     * @param RequestInterface $responseTransaction
     * @param string            $clientName
     */
    public function __construct(RequestInterface $responseTransaction, string $clientName)
    {
        $this->requestTransaction = $responseTransaction;
        $this->clientName = $clientName;
    }

    /**
     * Return the transaction
     * @return RequestInterface
     */
    public function getTransaction(): RequestInterface
    {
        return $this->requestTransaction;
    }

    /**
     * Set the transaction to a response
     *
     * @param RequestInterface $requestTransaction
     * @return PreTransactionEvent
     */
    public function setTransaction(RequestInterface $requestTransaction): PreTransactionEvent
    {
        $this->requestTransaction = $requestTransaction;
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
