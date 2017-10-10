<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @category Configuration
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class Configuration implements ConfigurationInterface
{
    /** @var string */
    protected $alias;

    /**
     * Configuration constructor.
     * @param string $alias
     */
    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root($this->alias)
            ->children()
                ->append($this->createClientsNode())->end()
            ->end();

        return $builder;
    }

    /**
     * Create clients node configuration
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    private function createClientsNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('clients');

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('base_uri')->defaultValue(null)->end()
                    ->arrayNode('headers')
                        ->prototype('scalar')->end()
                    ->end()
                    ->arrayNode('request_options')
                        ->children()
                            ->variableNode('allow_redirects')->end()
                            ->variableNode('auth')->defaultNull()->end()
                            ->variableNode('cert')->end()
                            ->floatNode('connect_timeout')->end()
                            ->scalarNode('decode_content')->end()
                            ->integerNode('delay')->end()
                            ->scalarNode('expect')->end()
                            ->enumNode('force_ip_resolve')
                                ->values(['v4', 'v6'])
                            ->end()
                            ->booleanNode('http_errors')->end()
                            ->variableNode('proxy')->end()
                            ->variableNode('query')->end()
                            ->floatNode('read_timeout')->end()
                            ->variableNode('ssl_key')->end()
                            ->booleanNode('stream')->end()
                            ->booleanNode('synchronous')->end()
                            ->scalarNode('verify')->end()
                            ->floatNode('timeout')->end()
                            ->scalarNode('version')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
