<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Log\Serializer\Denormalizer;

use Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer\RequestDenormalizer;

/**
 * Class RequestDenormalizerTest
 *
 * @category Denormalizer test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\Log\Serializer\Denormalizer
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class RequestDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestDenormalizer */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new RequestDenormalizer();
    }

    /**
     * Asserts that denormalization of an array results into a request object
     * with the correct properties set
     */
    public function testDenormalize()
    {
        $data = [
            'uri' => [
                'scheme' => 'https',
                'host' => 'localhost',
                'port' => 8081,
                'path' => '/path/of/exile'
            ],
            'headers' => [
                'some_headers'
            ],
            'protocol_version' => '1.1',
            'method' => 'POST',
            'body' => [
                'contents' => 'contents'
            ]
        ];

        $request = $this->subject->denormalize($data);

        $this->assertSame($data['uri']['host'], $request->getHost());
        $this->assertSame($data['uri']['port'], $request->getPort());
        $this->assertSame($data['uri']['path'], $request->getPath());
        $this->assertSame($data['uri']['scheme'], $request->getScheme());
        $this->assertSame('https://localhost:8081/path/of/exile', $request->getUrl());
        $this->assertSame($data['headers'], $request->getHeaders());
        $this->assertSame($data['method'], $request->getMethod());
        $this->assertSame($data['body']['contents'], $request->getBody());
    }
}
