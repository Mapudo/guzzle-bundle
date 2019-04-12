<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Builder;

use function array_shift;
use GuzzleHttp\HandlerStack;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder\HandlerDefinitionBuilder;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

class DefinitionBuilderTest extends TestCase
{
    /** @var HandlerDefinitionBuilder */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new HandlerDefinitionBuilder();
    }

    public function dataProviderBuild(): array
    {
        return [
            [
                [
                    'test_middleware' => [['method' => 'attach']],
                    'some_log_middleware' => [['method' => 'log']],
                ],
                'service("some_log_middleware").log()',
                'guzzle'
            ],
            [
                [
                    'test_middleware' => [['method' => 'attach']],
                    'some_log_middleware_custom_channel' => [['channel' => 'timings']],
                ],
                'service("some_log_middleware_custom_channel")',
                'timings'
            ],
        ];
    }

    /** @dataProvider dataProviderBuild */
    public function testBuild(array $middleware, string $expectedMiddlewareExpression, string $expectedChannel): void
    {
        $clientName = 'clientName';

        /** @var ContainerBuilder|ObjectProphecy $container */
        $container = $this->prophesize(ContainerBuilder::class);
        /** @noinspection PhpParamsInspection */
        $container
            ->setDefinition('guzzle_bundle.middleware.event_dispatch.clientName', Argument::type(Definition::class))
            ->shouldBeCalled();

        /** @noinspection PhpParamsInspection */
        $container
            ->setDefinition(
                'guzzle_bundle.middleware.log.clientName',
                Argument::that(static function (Definition $definition) use ($expectedChannel) {
                    Assert::assertSame('monolog.logger.' . $expectedChannel, (string)$definition->getArgument(0));
                    return true;
                })
            )
            ->shouldBeCalled();

        $handler = $this->subject->build($container->reveal(), $clientName, $middleware);

        // Test basic stuff
        Assert::assertSame(HandlerStack::class, $handler->getClass());
        Assert::assertSame([HandlerStack::class, 'create'], $handler->getFactory());

        $methodCalls = $handler->getMethodCalls();
        // Expected count is the number of the given middleware + the default event and log middleware expressions
        Assert::assertCount(4, $methodCalls);

        $customMiddlewareCall = array_shift($methodCalls);
        Assert::assertSame('push', array_shift($customMiddlewareCall));
        /** @var Expression[] $customMiddlewareExpressions */
        $customMiddlewareExpressions = array_shift($customMiddlewareCall);
        $customMiddlewareExpression = array_shift($customMiddlewareExpressions);
        Assert::assertInstanceOf(Expression::class, $customMiddlewareExpression);
        Assert::assertSame('service("test_middleware").attach()', $customMiddlewareExpression->__toString());

        $customLogMiddleware = array_shift($methodCalls);
        Assert::assertSame('push', array_shift($customLogMiddleware));
        $customLogMiddleWareExpressions = array_shift($customLogMiddleware);
        $customLogMiddleWareExpression = array_shift($customLogMiddleWareExpressions);
        Assert::assertInstanceOf(Expression::class, $customLogMiddleWareExpression);
        Assert::assertSame($expectedMiddlewareExpression, (string)$customLogMiddleWareExpression);

        $logMiddlewareCall = array_shift($methodCalls);
        Assert::assertSame('push', array_shift($logMiddlewareCall));
        /** @var Expression[] $logMiddlewareExpressions */
        $logMiddlewareExpressions = array_shift($logMiddlewareCall);
        $logMiddlewareExpression = array_shift($logMiddlewareExpressions);
        Assert::assertInstanceOf(Expression::class, $logMiddlewareExpression);
        Assert::assertSame(
            'service("guzzle_bundle.middleware.log.clientName").log()',
            $logMiddlewareExpression->__toString()
        );

        $eventDispatchMiddlewareCall = array_shift($methodCalls);
        Assert::assertSame('unshift', array_shift($eventDispatchMiddlewareCall));
        /** @var Expression[] $eventDispatchMiddlewareExpressions */
        $eventDispatchMiddlewareExpressions = array_shift($eventDispatchMiddlewareCall);
        $eventDispatchMiddlewareExpression = array_shift($eventDispatchMiddlewareExpressions);
        Assert::assertInstanceOf(Expression::class, $eventDispatchMiddlewareExpression);
        Assert::assertSame(
            'service("guzzle_bundle.middleware.event_dispatch.clientName").dispatch()',
            $eventDispatchMiddlewareExpression->__toString()
        );
    }
}
