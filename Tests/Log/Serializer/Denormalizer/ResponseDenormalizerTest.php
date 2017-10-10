<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Log\Serializer\Denormalizer;

use Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer\ResponseDenormalizer;

/**
 * Class ResponseDenormalizerTest
 *
 * @category Log Denormalizer test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\Log\Serializer\Denormalizer
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class ResponseDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /** @var ResponseDenormalizer */
    protected $subject;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->subject = new ResponseDenormalizer();
    }

    /**
     * Asserts that an array containing response information can be
     * denormalized into a valid Response object
     */
    public function testDenormalize()
    {
        $data = [
            'status_code' => 200,
            'reason_phrase' => 'OK',
            'body' => [
                'contents' => 'sample_content',
            ],
            'headers' => [],
            'protocol_version' => '1.1'
        ];

        $response = $this->subject->denormalize($data);

        $this->assertSame($data['status_code'], $response->getStatusCode());
        $this->assertSame($data['reason_phrase'], $response->getReasonPhrase());
        $this->assertSame($data['body']['contents'], $response->getBody());
        $this->assertSame($data['headers'], $response->getHeaders());
        $this->assertSame($data['protocol_version'], $response->getProtocolVersion());
    }
}
