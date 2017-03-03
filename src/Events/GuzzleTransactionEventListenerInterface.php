<?php
namespace Mapudo\Bundle\GuzzleBundle\Events;

/**
 * Interface GuzzleTransactionEventListenerInterface
 *
 * @category Event interface
 * @package  Mapudo\Bundle\GuzzleBundle\Events
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
interface GuzzleTransactionEventListenerInterface
{
    /**
     * The prefix used for any guzzle event
     */
    const EVENT_PREFIX = 'guzzle_event';

    /**
     * The name of the event that is fired before a transaction
     */
    const EVENT_PRE_TRANSACTION = self::EVENT_PREFIX . '.pre_transaction';

    /**
     * The name of the event that is fired after a transaction
     */
    const EVENT_POST_TRANSACTION = self::EVENT_PREFIX . '.post_transaction';

    /**
     * Array holding the name of the events
     */
    const EVENTS = [
        self::EVENT_PRE_TRANSACTION,
        self::EVENT_POST_TRANSACTION,
    ];

    /**
     * Set the name of the client which uses this event listener.
     *
     * @param string $clientName The name of the client.
     * @return mixed
     */
    public function setClientName(string $clientName);
}
