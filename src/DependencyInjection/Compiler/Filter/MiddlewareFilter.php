<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter;

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class MiddlewareFilter
 *
 * This class is used to filter all middleware used for a specific client,
 * identified by the client name
 *
 * @category Compiler filter
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class MiddlewareFilter
{
    /**
     * Filters middleware that a client should not support by checking if the attributes of the service definition of
     * the middleware contain the name of the client. Alternatively, if no clients are given, the middleware is added
     * automatically (e.g. when you want to use a middleware for all clients, leave it blank).
     *
     * @param ContainerBuilder $container  The container which is used to get parameters and set definitions
     * @param string           $clientName The name of the client
     *
     * @return array
     */
    public function filter(ContainerBuilder $container, string $clientName): array
    {
        $middleware = [];

        foreach ($container->findTaggedServiceIds('guzzle.middleware') as $service => $tags) {
            foreach ($tags as $options) {
                if (!array_key_exists('client', $options) || $clientName === $options['client']) {
                    $middleware[$service][] = $options;
                }
            }
        }

        return $middleware;
    }
}
