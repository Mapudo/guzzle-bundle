<?php
namespace Mapudo\Bundle\GuzzleBundle\DataCollector;

use Mapudo\Bundle\GuzzleBundle\Log\Model\Message;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RequestDataCollector
 *
 * @category Data collector
 * @package  Mapudo\Bundle\GuzzleBundle\DataCollector
 * @link     http://www.mapudo.com
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 */
class RequestDataCollector extends DataCollector
{
    /** @var array */
    protected $messages = [];

    /**
     * {@inheritdoc}Adds the requests to the data array of the Data Collector
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data['requests'] = $this->messages;
    }

    /**
     * Return the total amount of HTTP calls.
     * @return int
     */
    public function getRequestCount(): int
    {
        return array_sum(array_map(function (array $requests) {
            return count($requests);
        }, $this->data['requests']));
    }

    /**
     * Adds a message to the message stack of the current collector instance.
     *
     * @param Message $message The message that should be added
     * @return RequestDataCollector
     */
    public function addMessage(Message $message): RequestDataCollector
    {
        $this->messages[$message->getClient() ?? 'generic'][] = $message;
        return $this;
    }

    /**
     * @return Message[]
     */
    public function getRequests(): array
    {
        return $this->data['requests'];
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'guzzle';
    }
}
