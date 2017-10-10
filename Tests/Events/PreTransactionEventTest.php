<?php

namespace Mapudo\Bundle\GuzzleBundle\Tests\Events;

use Mapudo\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use Psr\Http\Message\RequestInterface;

/**
 * Class PreTransactionEventTest
 *
 * @category Event test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class PreTransactionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Since the class doesn't really have any logic we just use one test
     * to check if the methods to what they should be
     */
    public function testEvent()
    {
        $initialRequestTransaction = $this->prophesize(RequestInterface::class);
        $clientName = 'clientName';

        $postTransactionEvent = new PreTransactionEvent($initialRequestTransaction->reveal(), $clientName);
        $this->assertSame($initialRequestTransaction->reveal(), $postTransactionEvent->getTransaction());
        $this->assertSame($clientName, $postTransactionEvent->getClientName());

        // Set another transaction and check if the getter still works
        $anotherRequestTransaction = $this->prophesize(RequestInterface::class);
        $postTransactionEvent->setTransaction($anotherRequestTransaction->reveal());

        $this->assertNotSame($initialRequestTransaction->reveal(), $postTransactionEvent->getTransaction());
        $this->assertSame($anotherRequestTransaction->reveal(), $postTransactionEvent->getTransaction());
    }
}
