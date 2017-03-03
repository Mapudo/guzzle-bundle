<?php
namespace Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer;

use Mapudo\Bundle\GuzzleBundle\Log\Model\Response;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Class ResponseDenormalizer
 *
 * @category Denormalizer
 * @package  Mapudo\Bundle\GuzzleBundle\Log\Serializer\Denormalizer
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class ResponseDenormalizer implements DenormalizerInterface
{
    /**
     * @inheritdoc
     */
    public function denormalize($data, $class = null, $format = null, array $context = []): Response
    {
        $response = new Response();
        $response
            ->setStatusCode($data['status_code'])
            ->setReasonPhrase($data['reason_phrase'])
            ->setBody($data['body']['contents'])
            ->setHeaders($data['headers'])
            ->setProtocolVersion($data['protocol_version']);

        return $response;
    }

    /**
     * @inheritdoc
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return is_array($data) && $type === Response::class;
    }
}
