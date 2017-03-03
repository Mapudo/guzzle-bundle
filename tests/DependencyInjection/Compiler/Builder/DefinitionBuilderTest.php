<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Builder;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder\DefinitionBuilder;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Class DefinitionBuilderTest
 *
 * @category Compiler test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class DefinitionBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var DefinitionBuilder */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new DefinitionBuilder();
    }

    /**
     * Test case to assert the handler definition is created correctly.
     */
    public function testGetHandlerDefinition()
    {
        $clientName = 'clientName';

        /** @var ContainerBuilder|ObjectProphecy $container */
        $container = $this->prophesize(ContainerBuilder::class);

        $middleware = ['test_middleware' => [
           ['method' => 'attach']
        ]];

        $handler = $this->subject->getHandlerDefinition($container->reveal(), $clientName, $middleware);

        // Test basic stuff
        $this->assertSame('%guzzle_http.handler_stack.class%', $handler->getClass());
        $this->assertSame(['%guzzle_http.handler_stack.class%', 'create'], $handler->getFactory());

        $methodCalls = $handler->getMethodCalls();
        // Count is the number of the given middleware + the default event and log middleware expressions
        $this->assertCount(3, $methodCalls);

        $customMiddlewareCall = array_shift($methodCalls);
        $this->assertSame('push', array_shift($customMiddlewareCall));
        /** @var Expression[] $customMiddlewareExpressions */
        $customMiddlewareExpressions = array_shift($customMiddlewareCall);
        $customMiddlewareExpression = array_shift($customMiddlewareExpressions);
        $this->assertInstanceOf(Expression::class, $customMiddlewareExpression);
        $this->assertSame('service("test_middleware").attach()', $customMiddlewareExpression->__toString());

        $logMiddlewareCall = array_shift($methodCalls);
        $this->assertSame('push', array_shift($logMiddlewareCall));
        /** @var Expression[] $logMiddlewareExpressions */
        $logMiddlewareExpressions = array_shift($logMiddlewareCall);
        $logMiddlewareExpression = array_shift($logMiddlewareExpressions);
        $this->assertInstanceOf(Expression::class, $logMiddlewareExpression);
        $this->assertSame(
            'service("guzzle_bundle.middleware.log.clientName").log()',
            $logMiddlewareExpression->__toString()
        );

        $eventDispatchMiddlewareCall = array_shift($methodCalls);
        $this->assertSame('unshift', array_shift($eventDispatchMiddlewareCall));
        /** @var Expression[] $eventDispatchMiddlewareExpressions */
        $eventDispatchMiddlewareExpressions = array_shift($eventDispatchMiddlewareCall);
        $eventDispatchMiddlewareExpression = array_shift($eventDispatchMiddlewareExpressions);
        $this->assertInstanceOf(Expression::class, $eventDispatchMiddlewareExpression);
        $this->assertSame(
            'service("guzzle_bundle.middleware.event_dispatch.clientName").dispatch()',
            $eventDispatchMiddlewareExpression->__toString()
        );
    }

    /**
     * Test case to assert the event middleware definition is created correctly.
     */
    public function testGetEventMiddlewareDefinition()
    {
        $clientName = 'clientName';
        $eventMiddleware = $this->subject->getEventMiddlewareDefinition($clientName);

        $this->assertCount(2, $eventMiddleware->getArguments());
        $this->assertSame('%mapudo.guzzle.middleware.event_dispatch_middleware.class%', $eventMiddleware->getClass());

        // Assert the Reference has been set correctly
        /** @var Reference $reference */
        $reference = $eventMiddleware->getArgument(0);
        $this->assertInstanceOf(Reference::class, $reference);
        $this->assertSame('event_dispatcher', $reference->__toString());
        $this->assertSame(ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $reference->getInvalidBehavior());

        // Assert the client name
        $this->assertSame($clientName, $eventMiddleware->getArgument(1));
    }

    /**
     * Test case to assert the log middleware definition is created correctly.
     */
    public function testGetLogMiddlewareDefinition()
    {
        $logMiddleware = $this->subject->getLogMiddlewareDefinition();

        $this->assertCount(3, $logMiddleware->getArguments());
        $this->assertSame('%mapudo.guzzle.middleware.log_middleware.class%', $logMiddleware->getClass());

        // Assert the references have been set correctly
        $arguments = ['monolog.logger.guzzle', 'guzzle_bundle.formatter', 'mapudo_bundle_guzzle.serializer'];
        for ($i = 0; $i < 3; $i++) {
            /** @var Reference $reference */
            $reference = $logMiddleware->getArgument($i);
            $this->assertInstanceOf(Reference::class, $reference);
            $this->assertSame($arguments[$i], $reference->__toString());
            $this->assertSame(ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $reference->getInvalidBehavior());
        }
    }
}
