<?php
namespace Mapudo\Bundle\GuzzleBundle\Log\Handler;

use Mapudo\Bundle\GuzzleBundle\DataCollector\RequestDataCollector;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Message;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Request;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Response;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class SymfonyProfilerHandler
 *
 * @category Log Handler
 * @package  Mapudo\Bundle\GuzzleBundle\Log\Handler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class SymfonyProfilerHandler extends AbstractProcessingHandler
{
    /** @var RequestDataCollector */
    protected $requestDataCollector;

    /** @var DenormalizerInterface */
    protected $denormalizer;

    /**
     * SymfonyProfilerHandler constructor.
     *
     * @param RequestDataCollector  $requestDataCollector
     * @param DenormalizerInterface $denormalizer
     * @param bool|int              $level
     * @param bool                  $bubble
     */
    public function __construct(
        RequestDataCollector  $requestDataCollector,
        DenormalizerInterface $denormalizer,
        $level = Logger::DEBUG,
        $bubble = true
    ) {
        parent::__construct($level, $bubble);
        $this->requestDataCollector = $requestDataCollector;
        $this->denormalizer = $denormalizer;
    }

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  array $record
     * @return void
     */
    protected function write(array $record)
    {
        $request = null;

        if ($record['context']) {
            $response = null;
            if (!empty($record['context']['response'])) {
                $response = $this->denormalizer->denormalize($record['context']['response'], Response::class);
            }

            if (!empty($record['context']['request'])) {
                /** @var Request $request */
                $request = $this->denormalizer->denormalize($record['context']['request'], Request::class);
                $request->setResponse($response);
            }
        }

        $message = new Message();
        $message
            ->setRequest($request)
            ->setMessage($record['message'] ?? null)
            ->setClient($record['context']['client'] ?? null);

        $this->requestDataCollector->addMessage($message);
    }
}
