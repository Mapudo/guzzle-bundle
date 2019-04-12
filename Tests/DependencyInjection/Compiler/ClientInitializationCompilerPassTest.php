<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler;

use GuzzleHttp\Client;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder\HandlerDefinitionBuilder;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\ClientInitializationCompilerPass;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\EventListenerFilter;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\MiddlewareFilter;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ClientInitializationCompilerPassTest
 *
 * @category Compiler pass test
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class ClientInitializationCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder|ObjectProphecy */
    protected $container;

    /** @var ClientInitializationCompilerPass|\PHPUnit_Framework_MockObject_MockObject */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->container = $this->prophesize(ContainerBuilder::class);

        // We create a partial mock here, because we can't mock the dependencies otherwise.
        // I know that it sucks, but that's life for you.
        $this->subject = $this
            ->getMockBuilder(ClientInitializationCompilerPass::class)
            ->disableOriginalConstructor()
            ->setMethods(['getDefinitionBuilder', 'getEventListenerFilter', 'getMiddlewareFilter'])
            ->getMock();
    }

    /**
     * @dataProvider dataProviderProcess
     *
     * @param array $config
     * @param array $middleware
     * @param array $events
     */
    public function testProcess(array $config, array $middleware, array $events)
    {
        $this
            ->container
            ->getParameter(Argument::exact('mapudo.guzzle.config'))
            ->willReturn($config)
            ->shouldBeCalled();

        /** @var MiddlewareFilter|ObjectProphecy $middlewareFilter */
        $middlewareFilter = $this->prophesize(MiddlewareFilter::class);
        $middlewareFilter
            ->filter(Argument::exact($this->container->reveal()), Argument::type('string'))
            ->willReturn($middleware)
            ->shouldBeCalled();

        $this
            ->subject
            ->expects($this->atLeastOnce())
            ->method('getMiddlewareFilter')
            ->willReturn($middlewareFilter->reveal());

        foreach ($config['clients'] as $clientName => $options) {
            /** @noinspection PhpParamsInspection */
            $this
                ->container
                ->setDefinition(
                    Argument::exact('guzzle.client.' . $clientName),
                    Argument::that(function (Definition $definition) use ($options) {
                        $this->assertSame(Client::class, $definition->getClass());
                        $this->assertTrue($definition->isPublic());
                        $this->assertCount(1, $definition->getArguments());
                        $clientArguments = $definition->getArgument(0);

                        $this->assertCount(20, $clientArguments);
                        $this->assertSame($options['base_uri'], $clientArguments['base_uri']);
                        $this->assertSame($options['headers'], $clientArguments['headers']);
                        foreach ($options['request_options'] as $requestOption => $value) {
                            $this->assertArrayHasKey($requestOption, $clientArguments);
                            $this->assertSame($value, $clientArguments[$requestOption]);
                        }

                        return true;
                    })
                )
                ->shouldBeCalled();
        }

        foreach ($events as $index => $event) {
            $tags = $event['tags'];
            $events[$index] = $tags;
        }

        /** @var EventListenerFilter|ObjectProphecy $eventListenerFilter */
        $eventListenerFilter = $this->prophesize(EventListenerFilter::class);
        $eventListenerFilter
            ->filter(Argument::exact($this->container->reveal()))
            ->willReturn($events)
            ->shouldBeCalled();

        $this
            ->subject
            ->expects($this->atLeastOnce())
            ->method('getEventListenerFilter')
            ->willReturn($eventListenerFilter->reveal());

        foreach ($events as $eventId => $tags) {
            /** @var Definition|ObjectProphecy $definition */
            $definition = $this->prophesize(Definition::class);
            $definition
                ->addMethodCall(Argument::exact('setClientName'), Argument::exact([$tags[0]['client']]))
                ->shouldBeCalled();

            $this
                ->container
                ->getDefinition(Argument::exact($eventId))
                ->willReturn($definition->reveal())
                ->shouldBeCalled();
        }

        $this->subject->process($this->container->reveal());
    }

    /**
     * Data provider for testing the process method with given sample config
     * @return array
     */
    public function dataProviderProcess(): array
    {
        return [
            [
                Yaml::parse(file_get_contents(__DIR__ . '/../../Resources/config/sample_config.yml'))['guzzle'],
                Yaml::parse(file_get_contents(__DIR__ . '/../../Resources/config/sample_middleware.yml'))['services'],
                Yaml::parse(file_get_contents(__DIR__ . '/../../Resources/config/sample_events.yml'))['services'],
            ]
        ];
    }

    /**
     * I just add those tests so we get the code coverage
     */
    public function testGetters()
    {
        $subject = new ClientInitializationCompilerPass();

        $this->assertInstanceOf(HandlerDefinitionBuilder::class, $subject->getDefinitionBuilder());
        $this->assertInstanceOf(EventListenerFilter::class, $subject->getEventListenerFilter());
        $this->assertInstanceOf(MiddlewareFilter::class, $subject->getMiddlewareFilter());
    }
}
