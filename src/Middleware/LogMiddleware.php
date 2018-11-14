<?php

namespace Mapudo\Bundle\GuzzleBundle\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class LogMiddleware
 *
 * @category Middleware
 * @package  Mapudo\Bundle\GuzzleBundle\Middleware
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class LogMiddleware
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var MessageFormatter */
    protected $formatter;

    /** @var NormalizerInterface */
    protected $normalizer;

    /** @var string|null */
    protected $clientName;

    /**
     * LogMiddleware constructor.
     *
     * @param LoggerInterface     $logger
     * @param MessageFormatter    $formatter
     * @param NormalizerInterface $normalizer
     */
    public function __construct(LoggerInterface $logger, MessageFormatter $formatter, NormalizerInterface $normalizer)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
        $this->normalizer = $normalizer;
    }

    /**
     * Log each request
     * @return \Closure
     */
    public function log(): \Closure
    {
        $logger    = $this->logger;
        $formatter = $this->formatter;

        return function (callable $handler) use ($logger, $formatter) {
            return function (RequestInterface $request, array $options) use ($handler, $logger, $formatter) {
                $duration = null;
                $options['on_stats'] = function (TransferStats $stats) use (&$duration) {
                    $duration = $stats->getTransferTime();
                };

                return $handler($request, $options)->then(
                    function (ResponseInterface $response) use ($logger, $request, $formatter, &$duration) {
                        $message = $formatter->format($request, $response);
                        $logger->info($message, $this->buildContext($request, $response, $duration));

                        return $response;
                    },
                    function ($reason) use ($logger, $request, $formatter, &$duration) {
                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                        $message  = $formatter->format($request, $response, $reason);
                        $logger->error($message, $this->buildContext($request, $response, $duration));

                        return \GuzzleHttp\Promise\rejection_for($reason);
                    }
                );
            };
        };
    }

    /**
     * Sets the client name. Used to distinguish between clients.
     *
     * @param string $clientName
     * @return LogMiddleware
     */
    public function setClientName(string $clientName): LogMiddleware
    {
        $this->clientName = $clientName;
        return $this;
    }

    private function buildContext(?RequestInterface $request, ?ResponseInterface $response, float $duration)
    {
        $request->getBody()->rewind();
        $context = [
            'request' => $this->normalizer->normalize($request),
            'response' => $this->normalizer->normalize($response),
            'client' => $this->clientName,
            'duration' => $duration,
        ];
        $context['response']['body']['contents'] = '';

        return $context;
    }
}
