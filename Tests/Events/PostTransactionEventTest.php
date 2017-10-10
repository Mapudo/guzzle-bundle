<?php
namespace Mapudo\Bundle\GuzzleBundle\Tests\Events;

use Mapudo\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PostTransactionEventTest
 *
 * @category Event test
 * @package  Mapudo\Bundle\GuzzleBundle\Tests\DependencyInjection
 * @author   Theo Tzaferis <theodoros.tzaferis@mapudo.com>
 * @link     http://www.mapudo.com
 */
class PostTransactionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Since the class doesn't really have any logic we just use one test
     * to check if the methods to what they should be
     */
    public function testEvent()
    {
        $initialResponseTransaction = $this->prophesize(ResponseInterface::class);
        $clientName = 'clientName';

        $postTransactionEvent = new PostTransactionEvent($initialResponseTransaction->reveal(), $clientName);
        $this->assertSame($initialResponseTransaction->reveal(), $postTransactionEvent->getTransaction());
        $this->assertSame($clientName, $postTransactionEvent->getClientName());

        // Set another transaction and check if the getter still works
        $anotherResponseTransaction = $this->prophesize(ResponseInterface::class);
        $postTransactionEvent->setTransaction($anotherResponseTransaction->reveal());

        $this->assertNotSame($initialResponseTransaction->reveal(), $postTransactionEvent->getTransaction());
        $this->assertSame($anotherResponseTransaction->reveal(), $postTransactionEvent->getTransaction());
    }
}
