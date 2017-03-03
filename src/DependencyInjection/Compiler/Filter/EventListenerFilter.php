<?php
namespace Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter;

use Mapudo\Bundle\GuzzleBundle\Events\GuzzleTransactionEventListenerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EventListenerFilter
 *
 * @category Compiler filter
 * @package  Mapudo\Bundle\GuzzleBundle\DependencyInjection\Compiler\Filter
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class EventListenerFilter
{
    /**
     * Filters event listeners by retrieving every service from the container
     * which is tagged with "kernel.event_listener" and then checks if the event tag
     * starts with the guzzle prefix
     *
     * @param ContainerBuilder $container The container which is used to get parameters and set definitions
     * @return array
     */
    public function filter(ContainerBuilder $container): array
    {
        $eventListeners = $container->findTaggedServiceIds('kernel.event_listener');
        return array_filter($eventListeners, function ($eventListener) {
            $options = reset($eventListener);
            return array_key_exists('client', $options) &&
                in_array($options['event'], GuzzleTransactionEventListenerInterface::EVENTS);
        });
    }
}
