<?php

namespace Mapudo\Bundle\GuzzleBundle\Twig;

class GuzzleExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecode')),
        );
    }

    public function jsonDecode(string $json, bool $assoc = null, int $depth = null, int $options = null)
    {
        return call_user_func_array('json_decode', func_get_args());
    }
}
