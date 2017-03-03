<?php
namespace Mapudo\Bundle\GuzzleBundle;

use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\ClientInitializationCompilerPass;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\SerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class GuzzleBundle
 *
 * @category Bundle
 * @package  Mapudo
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class GuzzleBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container
            ->addCompilerPass(new ClientInitializationCompilerPass())
            ->addCompilerPass(new SerializerPass());
    }
}
