<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/14/15
 * Time: 2:10 PM
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;
use Hitmeister\Component\Metrics\Metric;
use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Mockery as m;


class StatsDaemonHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Socket|m\MockInterface
     */
    private $mockSocket;

    /**
     * @var string
     */
    private $testHost = '127.0.0.1';

    /**
     * @var int
     */
    private $testPort = 8125;

    /**
     * @var int
     */
    private $testTimeout = 10;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockSocket = m::mock('\Hitmeister\Component\Metrics\Socket\Socket');
        $this->mockSocket->shouldReceive('close')->once();
        $this->mockSocket->shouldReceive('connectTimeout')->withArgs([$this->testHost.':'.$this->testPort, $this->testTimeout])->once();
    }

    /**
     *
     */
    protected function tearDown()
    {
        $this->mockSocket = null;
        m::close();

        parent::tearDown();
    }

    /**
     * @return StatsDaemonHandler
     */
    protected function getHandler()
    {
        /** @var Factory|m\MockInterface $mockFactory */
        $mockFactory = m::mock('\Hitmeister\Component\Metrics\Socket\Factory');
        $mockFactory->shouldReceive('createUdp4')->andReturn($this->mockSocket);
        return new StatsDaemonHandler($this->testHost, $this->testPort, $this->testTimeout, 'udp', $mockFactory);
    }

    /**
     *
     */
    public function testHandleOne()
    {
        $metric = new Metric('metric_name');
        $metric->setSampleRate(0.5);

        // Create expected message
        $expectedMessage = $metric->getName().':'.$metric->getValue().'|'.$metric->getType().'|@'.$metric->getSampleRate();

        // Test case
        $this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();

        // Handle
        $handler = $this->getHandler();
        $handler->handle($metric);
    }

    public function testHandleBatch()
    {
        /** @var Metric[] $batch */
        $batch = [
            new Metric('metric_name_batch1'),
            new Metric('metric_name_batch2'),
            new Metric('metric_name_batch3'),
            new Metric('metric_name_batch4'),
        ];

        // Build expected message
        $messages = [];
        foreach ($batch as $item) {
            $messages[] = $item->getName().':'.$item->getValue().'|'.$item->getType();
        }

        // Test case
        $this->mockSocket->shouldReceive('write')->withArgs([join("\n", $messages)])->once();

        // Handle
        $handler = $this->getHandler();
        $handler->handleBatch($batch);
    }

    public function testHandleBatchMtuExceed()
    {
        /** @var Metric[] $batch */
        $batch = [];
        for ($i = 0; $i < 100; $i++) {
            $batch[] = new Metric("metric_name");
        }

        // Build expected message
        $calls = $this->splitBatchByMtu($batch);

        // Test case
        foreach ($calls as $messages) {
            $this->mockSocket->shouldReceive('write')->withArgs([join("\n", $messages)]);
        }

        // Handle
        $handler = $this->getHandler();
        $handler->handleBatch($batch);
    }

    /**
     * @param Metric[] $batch
     * @param int $mtu
     * @return array
     */
    private function splitBatchByMtu(array $batch, $mtu = 1500)
    {
        $calls = [];
        $messages = [];
        foreach ($batch as $item) {
            $message = $item->getName().':'.$item->getValue().'|'.$item->getType();
            if (strlen(join("\n", $messages)) + strlen($message) + 1 <= $mtu) {
                $messages[] = $message;
            } else {
                $calls[] = $messages;
                $messages = [$message];
            }
        }
        // Final call
        if (!empty($messages)) {
            $calls[] = $messages;
        }
        return $calls;
    }
}