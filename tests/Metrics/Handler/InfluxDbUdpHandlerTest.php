<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 10:21
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Formatter\InfluxDbLineFormatter;
use Hitmeister\Component\Metrics\Handler\InfluxDb\UdpHandler;
use Hitmeister\Component\Metrics\Metric\CounterMetric;

class InfluxDbUdpHandlerTest extends SocketHandlerTestCase
{
	/**
	 * @var InfluxDbLineFormatter
	 */
	private static $formatter;

	/**
	 * @inheritdoc
	 */
	public static function setUpBeforeClass()
	{
		self::$formatter = new InfluxDbLineFormatter();
	}

	/**
	 * Tests one message handle
	 */
	public function testHandleOne()
	{
		$handler = new UdpHandler($this->testHost, $this->testPort, $this->testTimeout);
		$handler->setFactory($this->mockFactory);

		// Normal metric
		$metric1 = new CounterMetric('metric_name1', 10);
		$metric1->setSampleRate(0.5);

		$expectedMessage = self::$formatter->format($metric1);

		$this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();
		$handler->handle($metric1);
	}


	/**
	 * Tests batch of messages handle
	 */
	public function testHandleBatch()
	{
		$handler = new UdpHandler($this->testHost, $this->testPort, $this->testTimeout);
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
	}
}