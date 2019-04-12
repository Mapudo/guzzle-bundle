<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler;

use GuzzleHttp\Client;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder\HandlerDefinitionBuilder;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\EventListenerFilter;
use Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter\MiddlewareFilter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class ClientInitializationCompilerPass
 *
 * @category Compiler pass
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class ClientInitializationCompilerPass implements CompilerPassInterface
{
    /** @var HandlerDefinitionBuilder */
    protected $definitionBuilder;

    /** @var EventListenerFilter */
    protected $eventListenerFilter;

    /** @var MiddlewareFilter */
    protected $middlewareFilter;

    /**
     * ClientInitializationCompilerPass constructor.
     */
    public function __construct()
    {
        $this->definitionBuilder = new HandlerDefinitionBuilder();

        $this->eventListenerFilter = new EventListenerFilter();
        $this->middlewareFilter = new MiddlewareFilter();
    }

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container The container which is used to get parameters and set definitions
     */
    public function process(ContainerBuilder $container)
    {
        $config = $container->getParameter('mapudo.guzzle.config');

        foreach ($config['clients'] as $clientName => $options) {
            $middleware = $this->getMiddlewareFilter()->filter($container, $clientName);

            $clientArgument = [
                'base_uri' => $options['base_uri'],
                'handler' => $this->getDefinitionBuilder()->build($container, $clientName, $middleware)
            ];

            if (array_key_exists('headers', $options)) {
                $clientArgument['headers'] = $this->formatHeaders($options['headers']);
            }

            if (array_key_exists('request_options', $options)) {
                foreach ($options['request_options'] as $requestOption => $value) {
                    $clientArgument[$requestOption] = $value;
                }
            }

            $client = new Definition(Client::class);
            $client->addArgument($clientArgument);
            $client->setPublic(true);

            $clientName = sprintf('%s.client.%s', 'guzzle', $clientName);
            $container->setDefinition($clientName, $client);
        }

        $eventListeners = $this->getEventListenerFilter()->filter($container);
        foreach ($eventListeners as $eventListenerId => $tags) {
            $attributes = reset($tags);
            $container->getDefinition($eventListenerId)->addMethodCall('setClientName', [$attributes['client']]);
        }
    }

    /**
     * Clean up HTTP headers. YAML converts "-" into "_", however "_" is not allowed
     * in HTTP requests, so we have to change it again.
     *
     * @param array $headers The headers that should be cleaned up
     * @return array
     */
    protected function formatHeaders(array $headers): array
    {
        foreach ($headers as $header => $value) {
            unset($headers[$header]);
            $headers[str_replace('_', '-', $header)] = $value;
        }

        return $headers;
    }

    /**
     * @return HandlerDefinitionBuilder
     */
    public function getDefinitionBuilder(): HandlerDefinitionBuilder
    {
        return $this->definitionBuilder;
    }

    /**
     * @return EventListenerFilter
     */
    public function getEventListenerFilter(): EventListenerFilter
    {
        return $this->eventListenerFilter;
    }

    /**
     * @return MiddlewareFilter
     */
    public function getMiddlewareFilter(): MiddlewareFilter
    {
        return $this->middlewareFilter;
    }
}
