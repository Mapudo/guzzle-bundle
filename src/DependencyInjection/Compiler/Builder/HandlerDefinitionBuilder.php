<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Builder;

use function array_key_exists;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use function strpos;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\Expression;
use function reset;
use function sprintf;

/**
 * Class DefinitionBuilder
 * This class is used to create definitions used and needed for the compiler pass
 *
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
final class HandlerDefinitionBuilder implements DefinitionBuilderInterface
{
    /**
     * @param ContainerBuilder $container  Used to filter tagged services and set definitions
     * @param string           $clientName The name of the client used to build services and find tagged services
     * @param array            $middleware The middleware that should be added to the handler
     */
    public function build(ContainerBuilder $container, string $clientName, array $middleware = []): Definition
    {
        $handler = new Definition(HandlerStack::class);
        $handler->setFactory([HandlerStack::class, 'create']);

        // The default channel we want to log to is guzzle, however the user can alter this
        // by submitting the "channel" in the tags when registering the log middleware
        $logChannel = 'guzzle';

        foreach ($middleware as $id => $tags) {
            $attributes = reset($tags);

            if (array_key_exists('channel', $attributes) && strpos($id, 'log_middleware') !== false) {
                $logChannel = $attributes['channel'];
            }

            if (!empty($attributes['method'])) {
                $middlewareExpression = new Expression(sprintf('service("%s").%s()', $id, $attributes['method']));
            } else {
                $middlewareExpression = new Expression(sprintf('service("%s")', $id));
            }
            $handler->addMethodCall('push', [$middlewareExpression]);
        }

        $eventServiceName = sprintf('guzzle_bundle.middleware.event_dispatch.%s', $clientName);
        $container->setDefinition($eventServiceName, $this->getEventMiddlewareDefinition($clientName));

        $logServiceName = sprintf('guzzle_bundle.middleware.log.%s', $clientName);
        $container->setDefinition($logServiceName, $this->getLogMiddlewareDefinition($clientName, $logChannel));

        $eventExpression  = new Expression(sprintf('service("%s").dispatch()', $eventServiceName));
        $logExpression = new Expression(sprintf('service("%s").log()', $logServiceName));

        $handler->addMethodCall('push', [$logExpression]);
        $handler->addMethodCall('unshift', [$eventExpression]);

        return $handler;
    }

    private function getEventMiddlewareDefinition(string $clientName): Definition
    {
        return (new Definition('%mapudo.guzzle.middleware.event_dispatch_middleware.class%'))
            ->addArgument(new Reference('event_dispatcher'))
            ->addArgument($clientName);
    }

    private function getLogMiddlewareDefinition(string $clientName, string $logChannel): Definition
    {
        $logMiddleware = (new Definition('%mapudo.guzzle.middleware.log_middleware.class%'))
            ->addArgument(new Reference(sprintf('monolog.logger.%s', $logChannel)))
            ->addArgument(new Reference(MessageFormatter::class))
            ->addArgument(new Reference('mapudo_bundle_guzzle.serializer'));

        $logMiddleware->addMethodCall('setClientName', [$clientName]);

        return $logMiddleware;
    }
}
