<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Filter;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\EventListenerFilter;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class EventListenerFilterTest
 *
 * @category Compiler filter test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Filter
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class EventListenerFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventListenerFilter */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new EventListenerFilter();
    }

    /**
     * Asserts that services defined as events are filtered correctly.
     *
     * @dataProvider dataProviderFilter
     *
     * @param array $eventListener
     * @param array $expectedResult
     */
    public function testFilter(array $eventListener, array $expectedResult)
    {
        /** @var ContainerBuilder|ObjectProphecy $container */
        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->findTaggedServiceIds(Argument::exact('kernel.event_listener'))
            ->willReturn($eventListener)
            ->shouldBeCalled();

        $filteredEventListeners = $this->subject->filter($container->reveal());
        $this->assertSame($expectedResult, $filteredEventListeners);
    }

    /**
     * Data provider for testing that the events get filtered correctly with two data sets:
     *
     * 0) Service definition containing only valid configs, therefore input and output should be the same
     * 1) Service definition where one event does not match the requirements, therefore only one service should be there
     *
     * @return array
     */
    public function dataProviderFilter(): array
    {
        $parsedEvents = Yaml::parse(
            file_get_contents(__DIR__ . '/../../../Resources/config/sample_events.yml')
        )['services'];

        $events = [];
        foreach ($parsedEvents as $id => $options) {
            $events[$id] = $options['tags'];
        }

        $parsedInvalidEvents = Yaml::parse(
            file_get_contents(__DIR__ . '/../../../Resources/config/events_includes_invalid.yml')
        )['services'];

        $invalidEvents = [];
        foreach ($parsedInvalidEvents as $id => $options) {
            $invalidEvents[$id] = $options['tags'];
        }

        return [
            [$events,        $events],
            [$invalidEvents, ['guzzle.event.stuff' => $invalidEvents['guzzle.event.stuff']]],
        ];
    }
}
