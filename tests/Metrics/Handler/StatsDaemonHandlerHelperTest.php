<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/17/15
 * Time: 12:45 AM
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Formatter\StatsDaemonFormatter;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;
use Hitmeister\Component\Metrics\Metric\CounterMetric;
use Hitmeister\Component\Metrics\Metric\DummyMetric;
use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Mockery as m;

class StatsDaemonHandlerHelperTest extends \PHPUnit_Framework_TestCase
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

        $batches = StatsDaemonHandler::explodeByMtu($messages, 50);
        $this->assertCount(2, $batches);
        $this->assertCount(2, $batches[0]);
        $this->assertCount(1, $batches[1]);
    }
}