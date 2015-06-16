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

class StatsDaemonHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Socket|m\MockInterface
     */
    private $mockSocket;

    /**
     * @var Factory|m\MockInterface
     */
    private $mockFactory;

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
     * @var StatsDaemonFormatter
     */
    private static $formatter;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::$formatter = new StatsDaemonFormatter();
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->mockSocket = m::mock('\Hitmeister\Component\Metrics\Socket\Socket');
        $this->mockSocket->shouldReceive('close')->once();
        $this->mockSocket->shouldReceive('connectTimeout')->withArgs([$this->testHost.':'.$this->testPort, $this->testTimeout])->once();

        $this->mockFactory = m::mock('\Hitmeister\Component\Metrics\Socket\Factory');
        $this->mockFactory->shouldReceive('createUdp4')->andReturn($this->mockSocket);
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->mockSocket = null;
        $this->mockFactory = null;

        m::close();
        parent::tearDown();
    }

    /**
     * Tests one message handle
     */
    public function testHandleOne()
    {
        $handler = new StatsDaemonHandler($this->testHost, $this->testPort, $this->testTimeout);
        $handler->setFactory($this->mockFactory);

        // Normal metric
        $metric1 = new CounterMetric('metric_name1', 10);
        $metric1->setSampleRate(0.5);

        $expectedMessage = self::$formatter->format($metric1);

        $this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();
        $handler->handle($metric1);

        // Unsupported
        $metric2 = new DummyMetric('metric_name2', 20);

        $this->mockSocket->shouldNotReceive('write');
        $handler->handle($metric2);
    }

    /**
     * Tests batch of messages handle
     */
    public function testHandleBatch()
    {
        $handler = new StatsDaemonHandler($this->testHost, $this->testPort, $this->testTimeout);
        $handler->setFactory($this->mockFactory);

        // Normal messages
        $batch1 = [
            new CounterMetric('metric_name_batch1', 1),
            new CounterMetric('metric_name_batch2', 2),
            new CounterMetric('metric_name_batch3', 3),
            new CounterMetric('metric_name_batch4', 4),
        ];

        $expectedMessages = self::$formatter->formatBatch($batch1);

        $this->mockSocket->shouldReceive('write')->withArgs([join("\n", $expectedMessages)])->once();
        $handler->handleBatch($batch1);

        // Unsupported
        $batch2 = [
            new DummyMetric('metric_name_batch1', 1),
            new DummyMetric('metric_name_batch2', 2),
            new DummyMetric('metric_name_batch3', 3),
            new DummyMetric('metric_name_batch4', 4),
        ];

        $this->mockSocket->shouldNotReceive('write');
        $handler->handleBatch($batch2);
    }

    /**
     * Tests batch of messages handle
     */
    public function testHandleBatchExceedMtu()
    {
        $handler = new StatsDaemonHandler($this->testHost, $this->testPort, $this->testTimeout, 'udp', 50);
        $handler->setFactory($this->mockFactory);

        // Normal messages
        $batch1 = [
            new CounterMetric(str_repeat('a', 18), 1),
            new CounterMetric(str_repeat('b', 18), 2),
            new CounterMetric(str_repeat('c', 18), 3),
        ];

        $expectedMessages = self::$formatter->formatBatch($batch1);

        $this->mockSocket->shouldReceive('write')->withArgs([$expectedMessages[0]."\n".$expectedMessages[1]])->once();
        $this->mockSocket->shouldReceive('write')->withArgs([$expectedMessages[2]])->once();
        $handler->handleBatch($batch1);
    }
}