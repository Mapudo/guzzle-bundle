<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * Class DefinitionBuilder
 * This class is used to create definitions used and needed for the compiler pass
 *
 * @category Compiler builder
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class DefinitionBuilder
{
    /**
     * Creates a definition for the guzzle handler
     *
     * @param ContainerBuilder $container  Used to filter tagged services and set definitions
     * @param string           $clientName The name of the client used to build services and find tagged services
     * @param array            $middleware The middleware that should be added to the handler
     *
     * @return Definition
     */
    public function getHandlerDefinition(
        ContainerBuilder $container,
        string $clientName,
        array $middleware = []
    ): Definition {
        $handler = new Definition('%guzzle_http.handler_stack.class%');
        $handler->setFactory(['%guzzle_http.handler_stack.class%', 'create']);

        foreach ($middleware as $id => $tags) {
            $attributes = reset($tags);
            $middlewareExpression = new Expression(sprintf('service("%s").%s()', $id, $attributes['method']));
            $handler->addMethodCall('push', [$middlewareExpression]);
        }

        $eventServiceName = sprintf('guzzle_bundle.middleware.event_dispatch.%s', $clientName);
        $container->setDefinition($eventServiceName, $this->getEventMiddlewareDefinition($clientName));

        $logServiceName = sprintf('guzzle_bundle.middleware.log.%s', $clientName);
        $container->setDefinition($logServiceName, $this->getLogMiddlewareDefinition($clientName));

        $eventExpression  = new Expression(sprintf('service("%s").dispatch()', $eventServiceName));
        $logExpression = new Expression(sprintf('service("%s").log()', $logServiceName));

        $handler->addMethodCall('push', [$logExpression]);
        $handler->addMethodCall('unshift', [$eventExpression]);

        return $handler;
    }

    /**
     * Creates a definition of the event middleware
     *
     * @param string $clientName The name of the client used for the event middleware
     * @return Definition
     */
    public function getEventMiddlewareDefinition(string $clientName): Definition
    {
        return (new Definition('%mapudo.guzzle.middleware.event_dispatch_middleware.class%'))
            ->addArgument(new Reference('event_dispatcher'))
            ->addArgument($clientName);
    }

    /**
     * Creates a definition of the log middleware
     *
     * @param string $clientName
     * @return Definition
     */
    public function getLogMiddlewareDefinition(string $clientName): Definition
    {
        $logMiddleware = (new Definition('%mapudo.guzzle.middleware.log_middleware.class%'))
            ->addArgument(new Reference('monolog.logger.guzzle'))
            ->addArgument(new Reference('guzzle_bundle.formatter'))
            ->addArgument(new Reference('mapudo_bundle_guzzle.serializer'));

        $logMiddleware->addMethodCall('setClientName', [$clientName]);

        return $logMiddleware;
    }
}
