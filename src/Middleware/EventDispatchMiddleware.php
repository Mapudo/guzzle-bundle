<?php
namespace Mapudo\Bundle\GuzzleBundle\Middleware;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Mapudo\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Mapudo\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use Mapudo\Bundle\GuzzleBundle\Events\GuzzleTransactionEventListenerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventDispatchMiddleware
 *
 * @category Middleware
 * @package  Mapudo\Bundle\GuzzleBundle\Middleware
 * @link     http://www.mapudo.com
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 */
class EventDispatchMiddleware
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $clientName;

    public function __construct(EventDispatcherInterface $eventDispatcher, string $clientName)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->clientName = $clientName;
    }

    public function dispatch(): Closure
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                $preTransactionEvent = new PreTransactionEvent($request, $this->clientName);
                $this
                    ->eventDispatcher
                    ->dispatch(
                        GuzzleTransactionEventListenerInterface::EVENT_PRE_TRANSACTION,
                        $preTransactionEvent
                    );

                /** @var PromiseInterface $promise */
                $promise = $handler($preTransactionEvent->getTransaction(), $options);

                return $promise->then(
                    function (ResponseInterface $response) {
                        $postTransactionEvent = new PostTransactionEvent($response, $this->clientName);
                        $this
                            ->eventDispatcher
                            ->dispatch(
                                GuzzleTransactionEventListenerInterface::EVENT_POST_TRANSACTION,
                                $postTransactionEvent
                            );

                        return $postTransactionEvent->getTransaction();
                    }
                );
            };
        };
    }
}
