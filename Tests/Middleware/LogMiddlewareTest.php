<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Middleware;

use GuzzleHttp\MessageFormatter;
use Mapudo\Bundle\GuzzleBundle\Middleware\LogMiddleware;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class LogMiddlewareTest
 *
 * @category Middleware test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\Middleware
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class LogMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var LoggerInterface|ObjectProphecy */
    protected $logger;

    /** @var MessageFormatter|ObjectProphecy */
    protected $messageFormatter;

    /** @var NormalizerInterface|ObjectProphecy */
    protected $normalizer;

    /** @var LogMiddleware */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->messageFormatter = $this->prophesize(MessageFormatter::class);
        $this->normalizer = $this->prophesize(NormalizerInterface::class);

        $this->subject = new LogMiddleware(
            $this->logger->reveal(),
            $this->messageFormatter->reveal(),
            $this->normalizer->reveal()
        );
    }

    /**
     * Asserts the log function is called correctly
     */
    public function testLog()
    {
        $this->subject->log();
    }
}
