<?php
declare(strict_types=1);

namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface DefinitionBuilderInterface
{
    public function build(ContainerBuilder $container, string $clientName, array $context = []): Definition;
}
