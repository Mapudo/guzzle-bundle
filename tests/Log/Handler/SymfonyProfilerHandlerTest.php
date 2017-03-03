<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Log\Handler;

use Mapudo\Bundle\GuzzleBundle\DataCollector\RequestDataCollector;
use Mapudo\Bundle\GuzzleBundle\Log\Handler\SymfonyProfilerHandler;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Message;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Request;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Response;
use Monolog\Logger;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class SymfonyProfilerHandlerTest
 *
 * @category Log Handler Test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\Log\Handler
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class SymfonyProfilerHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var SymfonyProfilerHandler */
    protected $subject;

    /** @var RequestDataCollector|ObjectProphecy */
    protected $requestDataCollector;

    /** @var DenormalizerInterface|ObjectProphecy */
    protected $denormalizer;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->requestDataCollector = $this->prophesize(RequestDataCollector::class);
        $this->denormalizer = $this->prophesize(DenormalizerInterface::class);

        $this->subject = new SymfonyProfilerHandler(
            $this->requestDataCollector->reveal(),
            $this->denormalizer->reveal()
        );
    }

    /**
     * @dataProvider dataProviderWrite
     *
     * @param bool $recordHasContext
     * @param bool $contextHasResponse
     * @param bool $contextHasRequest
     */
    public function testWrite(bool $recordHasContext, bool $contextHasResponse, bool $contextHasRequest)
    {
        $recordMessage = 'testMessage';
        $record = [
            'context' => [],
            'level' => Logger::DEBUG,
            'extra' => [],
            'message' => $recordMessage
        ];
        $request = null;

        if ($recordHasContext) {
            $response = null;
            if ($contextHasResponse) {
                $record['context']['response'] = 'not empty';
                $response = $this->prophesize(Response::class);
                $response = $response->reveal();

                $this
                    ->denormalizer
                    ->denormalize(Argument::any(), Argument::exact(Response::class))
                    ->willReturn($response)
                    ->shouldBeCalled();
            }

            if ($contextHasRequest) {
                $record['context']['request'] = 'not empty';
                /** @var Request|ObjectProphecy $request */
                $request = $this->prophesize(Request::class);
                $request
                    ->setResponse(Argument::exact($response))
                    ->willReturn($request->reveal())
                    ->shouldBeCalled();
                $request = $request->reveal();

                $this
                    ->denormalizer
                    ->denormalize(Argument::any(), Argument::exact(Request::class))
                    ->willReturn($request)
                    ->shouldBeCalled();
            }
        }

        $this
            ->requestDataCollector
            ->addMessage(Argument::that(function (Message $message) use ($request, $recordMessage) {
                $this->assertSame($request, $message->getRequest());
                $this->assertSame($recordMessage, $message->getMessage());
                return true;
            }))
            ->willReturn($this->requestDataCollector->reveal())
            ->shouldBeCalled();

        // Since the write method is protected we call the handle method which later calls the write method
        $this->subject->handle($record);
    }

    /**
     * Data provider for the write test with five data sets:
     *
     * 0) Record has context, context has response, context has request
     * 1) Record has context, context has response, context has no request
     * 2) Record has context, context has no response, context has request
     * 3) Record has context, context has no response, context has no request
     * 4) Record has no context, therefore context has no response and context has no request
     *
     * @return array
     */
    public function dataProviderWrite(): array
    {
        return [
            [true,  true,  true],
            [true,  true,  false],
            [true,  false, true],
            [true,  false, false],
            [false, false, false]
        ];
    }
}
