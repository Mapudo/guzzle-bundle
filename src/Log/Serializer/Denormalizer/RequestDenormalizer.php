<?php
namespace Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer;

use GuzzleHttp\Psr7\Uri;
use Mapudo\Bundle\GuzzleBundle\Log\Model\Request;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class RequestDenormalizer
 *
 * @category
 * @package  Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class RequestDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class = null, $format = null, array $context = []): Request
    {
        $uri = Uri::fromParts($data['uri']);

        $request = new Request();
        $request
            ->setHost($uri->getHost())
            ->setPort($uri->getPort())
            ->setUrl(urldecode((string) $uri))
            ->setPath($uri->getPath())
            ->setScheme($uri->getScheme())
            ->setHeaders($data['headers'] ?? [])
            ->setProtocolVersion($data['protocol_version'])
            ->setMethod($data['method'])
            ->setBody($data['body'] ? $data['body']['contents'] : null);

        return $request;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return is_array($data) && $type === Request::class;
    }
}
