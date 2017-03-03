<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Filter;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\MiddlewareFilter;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Yaml;

/**
 * Class MiddlewareFilterTest
 *
 * @category Filter compiler test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler\Filter
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class MiddlewareFilterTest extends \PHPUnit_Framework_TestCase
{
    /** @var MiddlewareFilter */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new MiddlewareFilter();
    }

    /**
     * @dataProvider dataProviderFilter
     *
     * @param array  $middleware
     * @param string $clientName
     * @param array  $expectedResult
     */
    public function testFilter(array $middleware, string $clientName, array $expectedResult)
    {
        /** @var ContainerBuilder|ObjectProphecy $container */
        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->findTaggedServiceIds(Argument::exact('guzzle.middleware'))
            ->willReturn($middleware)
            ->shouldBeCalled();

        $filteredMiddleware = $this->subject->filter($container->reveal(), $clientName);
        $this->assertSame($expectedResult, $filteredMiddleware);
    }

    /**
     * @return array
     */
    public function dataProviderFilter(): array
    {
        $parsedMiddleware = Yaml::parse(
            file_get_contents(__DIR__ . '/../../../Resources/config/sample_middleware.yml')
        )['services'];

        $middleware = [];
        foreach ($parsedMiddleware as $id => $options) {
            $middleware[$id] = $options['tags'];
        }

        return [
            [$middleware, 'test_client', $middleware],
            [$middleware, 'false_client', []],
        ];
    }
}
