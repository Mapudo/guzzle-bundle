<?php
declare(strict_types=1);

namespace Mapudo\Bundle\GuzzleBundle\Middleware\Authentication;

use Closure;

interface AuthenticationMiddlewareInterface
{
    public function authenticate(): Closure;
}
