<?php
declare(strict_types=1);

namespace Mapudo\Bundle\GuzzleBundle\Middleware\Authentication;

use Closure;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;

final class ApiKeyMiddleware implements AuthenticationMiddlewareInterface
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $apiKeyParameterName;

    public function __construct(string $apiKey, string $apiKeyParameterName = 'key')
    {
        $this->apiKey = $apiKey;
        $this->apiKeyParameterName = $apiKeyParameterName;
    }

    public function authenticate(): Closure
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $newRequest = $request->withUri(Uri::withQueryValue(
                    $request->getUri(),
                    $this->apiKeyParameterName,
                    $this->apiKey
                ));

                return $handler($newRequest, $options);
            };
        };
    }
}
