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

    public function testGetRequests()
    {
        // Since we didn't initialize the RequestDataCollector at all an empty array should be returned here.
        $requests = $this
            ->subject
            ->getRequests();

        $this->assertEmpty($requests);

        /** @var ObjectProphecy|Message $message */
        $message = $this->prophesize(Message::class);
        $this
            ->subject
            ->addMessage($message->reveal());

        $this
            ->subject
            ->collect($this->prophesize(Request::class)->reveal(), $this->prophesize(Response::class)->reveal());

        // Now after collecting the messages, we should get a result.
        $requests = $this
            ->subject
            ->getRequests();

        $this->assertCount(1, $requests);
        $this->assertSame($message->reveal(), array_shift($requests['generic']));
    }

    /**
     * Asserts that the name is set correctly
     */
    public function testGetName()
    {
        $this->assertSame('guzzle', $this->subject->getName());
    }

    public function testReset(): void
    {
        $request = $this->prophesize(Request::class);
        $response = $this->prophesize(Response::class);

        // add a message and check whether the message is added to data
        $this->subject->addMessage((new Message())->setMessage('my message'));
        $this->subject->collect($request->reveal(), $response->reveal());
        $this->assertCount(1, $this->subject->getRequests());

        // reset the DataCollector and check whether the requests are empty
        $this->subject->reset();
        $this->assertCount(0, $this->subject->getRequests());
    }
}
