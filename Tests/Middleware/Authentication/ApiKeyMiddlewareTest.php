<?php
declare(strict_types=1);

namespace Mapudo\Bundle\GuzzleBundle\Tests\Middleware\Authentication;

use GuzzleHttp\Psr7\Request;
use Mapudo\Bundle\GuzzleBundle\Middleware\Authentication\ApiKeyMiddleware;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class ApiKeyMiddlewareTest extends TestCase
{
    /** @var ApiKeyMiddleware */
    private $apiKeyMiddleware;

    protected function setUp()
    {
        $this->apiKeyMiddleware = new ApiKeyMiddleware(
            'totallySecretApiKey',
            'api_key'
        );
    }

    public function testAuthenticate()
    {
        $handler = static function (RequestInterface $request) {
            Assert::assertSame('api_key=totallySecretApiKey', $request->getUri()->getQuery());
            Assert::assertSame(
                'https://api.google.com/calendar?api_key=totallySecretApiKey',
                (string)$request->getUri()
            );
        };

        $request = new Request('GET', 'https://api.google.com/calendar');
        $options = ['options_are_unimportant_here'];

        $this->apiKeyMiddleware->authenticate()($handler)($request, $options);
    }
}
