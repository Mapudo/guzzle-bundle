<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\GuzzleExtension;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Class GuzzleExtensionTest
 *
 * @category Extension test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class GuzzleExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests the load method of the extension.
     */
    public function testLoad()
    {
        /** @var ObjectProphecy|ContainerBuilder $container */
        $container = $this->prophesize(ContainerBuilder::class);
        $container->hasExtension(Argument::any())->shouldBeCalled();
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->getParameterBag()->shouldBeCalled()->willReturn($this->prophesize(ParameterBag::class)->reveal());
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->setParameter(Argument::exact('mapudo.guzzle.config'), Argument::any())->shouldBeCalled();

        $guzzleExtension = new GuzzleExtension();
        $guzzleExtension->load([], $container->reveal());
    }

    /**
     * @dataProvider dataProviderPrepend
     *
     * @param bool $hasMonologBundle
     */
    public function testPrepend(bool $hasMonologBundle)
    {
        $bundles = $hasMonologBundle ? ['MonologBundle' => 'exists'] : [];

        /** @var ObjectProphecy|ContainerBuilder $containerBuilder */
        $containerBuilder = $this->prophesize(ContainerBuilder::class);
        $containerBuilder
            ->getParameter(Argument::exact('kernel.bundles'))
            ->willReturn($bundles)
            ->shouldBeCalled();

        if ($hasMonologBundle) {
            $containerBuilder
                ->prependExtensionConfig(Argument::exact('monolog'), Argument::exact([
                    'channels' => ['guzzle']
                ]))
                ->shouldBeCalled();
        } else {
            $containerBuilder
                ->prependExtensionConfig(Argument::any(), Argument::any())
                ->shouldNotBeCalled();
        }

        $guzzleExtension = new GuzzleExtension();
        $guzzleExtension->prepend($containerBuilder->reveal());
    }

    /**
     * Data provider for testing the prepend method with two data sets:
     *
     * 0) The container has a MonologBundle registered
     * 1) The container has no MonologBundle registered (shouldn't happen in prod though)
     *
     * @return array
     */
    public function dataProviderPrepend(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
