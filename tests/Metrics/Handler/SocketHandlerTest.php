<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/17/15
 * Time: 12:45 AM
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Handler\SocketHandler;

class SocketHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests explode function
     */
    public function testExplodeByMtu()
    {
        $messages = [
            str_repeat('a', 22),
            str_repeat('b', 22),
            str_repeat('c', 22),
        ];

        $batches = SocketHandler::explodeByMtu($messages, 50);
        $this->assertCount(2, $batches);
        $this->assertCount(2, $batches[0]);
        $this->assertCount(1, $batches[1]);
    }
}