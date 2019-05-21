<?php
declare(strict_types=1);

namespace Mapudo\Bundle\GuzzleBundle\Middleware\Authentication;

use Closure;
use Psr\Http\Message\RequestInterface;

final class ApiKeyMiddleware implements AuthenticationMiddlewareInterface
{
    /** @var string */
    private $apiKey;

    public function __construct(string $apiKey, string $apiKeyParameterName = 'key')
    {
        $this->apiKey = $apiKey;
    }

    public function authenticate(): Closure
    {
        return static function (callable $handler) {
            return static function (RequestInterface $request, array $options) use ($handler) {
                // $request->
                return $handler;
            };
        };
    }
}
