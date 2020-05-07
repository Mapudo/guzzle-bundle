<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Middleware;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Promise\PromiseInterface;
use Mapudo\Bundle\GuzzleBundle\Events\GuzzleTransactionEventListenerInterface;
use Mapudo\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Mapudo\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use Mapudo\Bundle\GuzzleBundle\Middleware\EventDispatchMiddleware;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventDispatchMiddlewareTest
 *
 * @category Middleware Test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\Middleware
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class EventDispatchMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var string */
    protected $clientName;

    /** @var EventDispatchMiddleware */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->clientName = 'test_client';

        $this->subject = new EventDispatchMiddleware($this->eventDispatcher->reveal(), $this->clientName);
    }

    /**
     * Asserts the dispatch event works as intended.
     */
    public function testDispatch()
    {
        $this
            ->eventDispatcher
            ->dispatch(
                Argument::exact(GuzzleTransactionEventListenerInterface::EVENT_PRE_TRANSACTION),
                Argument::that(function (PreTransactionEvent $preTransactionEvent) {
                    $this->assertSame($this->clientName, $preTransactionEvent->getClientName());
                    return true;
                })
            )
            ->shouldBeCalled();

        $this
            ->eventDispatcher
            ->dispatch(
                Argument::exact(GuzzleTransactionEventListenerInterface::EVENT_POST_TRANSACTION),
                Argument::that(function (PostTransactionEvent $postTransactionEvent) {
                    $this->assertSame($this->clientName, $postTransactionEvent->getClientName());
                    return true;
                })
            )
            ->shouldBeCalled();


        /** @var PromiseInterface|ObjectProphecy $handlerPromise */
        $handlerPromise = $this->prophesize(PromiseInterface::class);
        $handlerPromise
            ->then(Argument::that(function (\Closure $responseClosure) {
                $response = $this->prophesize(ResponseInterface::class);
                $closureResponse = $responseClosure($response->reveal());
                $this->assertSame($response->reveal(), $closureResponse);
                return true;
            }))
            ->shouldBeCalled();

        /** @var CurlHandler|ObjectProphecy $curlHandler */
        $curlHandler = $this->prophesize(CurlHandler::class);
        $curlHandler
            ->__invoke(Argument::any(), Argument::any())
            ->willReturn($handlerPromise->reveal())
            ->shouldBeCalled();

        $dispatchClosure = $this->subject->dispatch();
        $closure = $dispatchClosure($curlHandler->reveal());
        $closure($this->prophesize(RequestInterface::class)->reveal(), []);
    }
}
