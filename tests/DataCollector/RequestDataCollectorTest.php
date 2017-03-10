<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\DataCollector;

use Mapudo\Bundle\GuzzleBundle\DataCollector\RequestDataCollector;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Message;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RequestDataCollectorTest
 *
 * @category Data Collector Test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DataCollector
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class RequestDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestDataCollector */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new RequestDataCollector();
    }

    /**
     * Assures that collecting data works as intended.
     */
    public function testCollect()
    {
        /** @var ObjectProphecy|Message $firstMessage */
        $firstMessage = $this->prophesize(Message::class);
        /** @var ObjectProphecy|Message $secondMessage */
        $secondMessage = $this->prophesize(Message::class);

        $this
            ->subject
            ->addMessage($firstMessage->reveal())
            ->addMessage($secondMessage->reveal());

        $this
            ->subject
            ->collect($this->prophesize(Request::class)->reveal(), $this->prophesize(Response::class)->reveal());

        $this->assertSame(
            ['generic' => [$firstMessage->reveal(), $secondMessage->reveal()]],
            $this->subject->getRequests()
        );
        $this->assertSame(2, $this->subject->getRequestCount());
    }

    /**
     * Asserts that the name is set correctly
     */
    public function testGetName()
    {
        $this->assertSame('guzzle', $this->subject->getName());
    }
}
