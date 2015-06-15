<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 15/06/15
 * Time: 13:44
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Handler\InfluxDBv08Handler;
use Hitmeister\Component\Metrics\Metric;
use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Mockery as m;

class InfluxDBv08HandlerUdpTest extends InfluxDBv08HandlerTestCase
{
	/**
	 * @var Socket|m\MockInterface
	 */
	private $mockSocket;

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

		parent::tearDown();
	}

	/**
	 * @return InfluxDBv08Handler
	 */
	protected function getHandler()
	{
		/** @var Factory|m\MockInterface $mockFactory */
		$mockFactory = m::mock('\Hitmeister\Component\Metrics\Socket\Factory');
		$mockFactory->shouldReceive('createUdp4')->andReturn($this->mockSocket);
		$handler = new InfluxDBv08Handler();
		$handler->useUdp($this->testHost, $this->testPort, $this->testTimeout, $mockFactory);
		return $handler;
	}

	/**
	 *
	 */
	public function testHandleOne()
	{
		list($metric, $expectedMessage) = $this->getMetricMessageOne();

		// Test case
		$this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();

		// Handle
		$handler = $this->getHandler();
		$handler->handle($metric);
	}

	public function testHandleBatch()
	{
		list($batch, $expectedMessage) = $this->getMetricMessageBatch();

		// Test case
		$this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();

		// Handle
		$handler = $this->getHandler();
		$handler->handleBatch($batch);
	}

	/**
	 *
	 */
	public function testHandleTimeAdjust()
	{
		$now = time();

		$metric = new Metric('metric_name', 10, Metric::TYPE_COUNT, ['env' => 'prod']);
		$metric->setTime($now, Metric::PRECISION_SECONDS);

		// Create expected message
		$expectedMessage = [['name' => $metric->getName(), 'columns' => ['env', 'value', 'time'], 'points' => [['prod', $metric->getValue(), $now*1000]]]];
		$expectedMessage = json_encode($expectedMessage);

		// Test case
		$this->mockSocket->shouldReceive('write')->withArgs([$expectedMessage])->once();

		// Handle
		$handler = $this->getHandler();
		$handler->handle($metric);
	}
}