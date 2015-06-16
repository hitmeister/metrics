<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 3:24 PM
 */

namespace Hitmeister\Component\Metrics\Tests;

use Hitmeister\Component\Metrics\Buffer\BufferInterface;
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\AbstractMetric;
use Hitmeister\Component\Metrics\Metric\CounterMetric;
use Hitmeister\Component\Metrics\Metric\GaugeMetric;
use Hitmeister\Component\Metrics\Metric\MemoryMetric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;
use Hitmeister\Component\Metrics\Metric\TimerMetric;
use Hitmeister\Component\Metrics\Metric\UniqueMetric;
use Mockery as m;

class CollectorTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @inheritdoc
	 */
	public function tearDown()
	{
		m::close();
		parent::tearDown();
	}

	/**
	 * Tests getters and setters
	 */
	public function testSetGet()
	{
		$collector = new Collector();

		// Set get prefix
		$this->assertEquals('', $collector->getMetricPrefix());
		$collector->setMetricPrefix('prefix_');
		$this->assertEquals('prefix_', $collector->getMetricPrefix());

		// Set get tags
		$this->assertFalse($collector->hasTags());
		$this->assertCount(0, $collector->getTags());
		$collector->setTags(['env' => 'prod']);
		$this->assertArrayHasKey('env', $collector->getTags());
		$collector->removeTag('env');
		$this->assertArrayNotHasKey('env', $collector->getTags());
		$collector->addTag('server', 'web01');
		$this->assertArrayHasKey('server', $collector->getTags());
	}

	/**
	 * Tests setter/getter of buffer
	 */
	public function testSetGetBuffer()
	{
		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$this->assertEquals($mockBuffer, $collector->getBuffer());
	}

	/**
	 * Tests setter of handler
	 */
	public function testSetHandler()
	{
		/** @var m\MockInterface|HandlerInterface $mockHandler */
		$mockHandler = m::mock('\Hitmeister\Component\Metrics\Handler\HandlerInterface');

		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('setHandler')->withArgs([$mockHandler])->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->setHandler($mockHandler);
	}

	/**
	 * Tests add to buffer
	 */
	public function testAddToBuffer()
	{
		$metric = $this->mockMetric();

		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('add')->withArgs([$metric])->andReturn(true)->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->buffer($metric);
	}

	/**
	 * Tests add batch to buffer
	 */
	public function testAddBatchToBuffer()
	{
		$batch = [
			$this->mockMetric(),
			$this->mockMetric(),
		];

		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('addBatch')->withArgs([$batch])->andReturn(true)->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->bufferBatch($batch);
	}

	/**
	 * Tests add empty batch to buffer
	 */
	public function testAddEmptyBatchToBuffer()
	{
		$batch = [];

		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldNotReceive('addBatch');

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->bufferBatch($batch);
	}

	/**
	 * Returns test cases for counter
	 *
	 * @return array
	 */
	public function counterDataProvider()
	{
		return [['counter'], ['increment'], ['decrement']];
	}

	/**
	 * Tests counter functions
	 *
	 * @param string $function
	 * @dataProvider counterDataProvider
	 */
	public function testCounter($function)
	{
		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('addBatch')->with(m::on(function($metrics){
			if (count($metrics) == 1 && $metrics[0] instanceof CounterMetric) {
				return true;
			}
			return false;
		}))->andReturn(true)->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->$function('metric_name', 10);
	}

	/**
	 * Tests counter batch functions
	 *
	 * @param string $function
	 * @dataProvider counterDataProvider
	 */
	public function testCounterBatch($function)
	{
		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('addBatch')->with(m::on(function($metrics){
			if (count($metrics) == 2) {
				if ($metrics[0] instanceof CounterMetric && $metrics[1] instanceof CounterMetric) {
					return true;
				}
			}
			return false;
		}))->andReturn(true)->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->$function(['metric_name1', 'metric_name2'], 10);
	}

	/**
	 * Returns test cases for basic metrics
	 *
	 * @return array
	 */
	public function basicDataProvider()
	{
		return [
			['timer', 'metric_name1', '\Hitmeister\Component\Metrics\Metric\TimerMetric'],
			['timer', ['metric_name1', 'metric_name2'], '\Hitmeister\Component\Metrics\Metric\TimerMetric'],
			['memory', 'metric_name1', '\Hitmeister\Component\Metrics\Metric\MemoryMetric'],
			['memory', ['metric_name1', 'metric_name2'], '\Hitmeister\Component\Metrics\Metric\MemoryMetric'],
			['gauge', 'metric_name1', '\Hitmeister\Component\Metrics\Metric\GaugeMetric'],
			['gauge', ['metric_name1', 'metric_name2'], '\Hitmeister\Component\Metrics\Metric\GaugeMetric'],
			['unique', 'metric_name1', '\Hitmeister\Component\Metrics\Metric\UniqueMetric'],
			['unique', ['metric_name1', 'metric_name2'], '\Hitmeister\Component\Metrics\Metric\UniqueMetric'],
		];
	}

	/**
	 * Tests timer function
	 *
	 * @param string $function
	 * @param mixed  $names
	 * @param string $expectedClass
	 * @dataProvider basicDataProvider
	 */
	public function testBasic($function, $names, $expectedClass)
	{
		$expectedCount = is_array($names) ? count($names) : 1;

		/** @var m\MockInterface|BufferInterface $mockBuffer */
		$mockBuffer = m::mock('\Hitmeister\Component\Metrics\Buffer\BufferInterface');
		$mockBuffer->shouldReceive('addBatch')->with(m::on(function($metrics) use($expectedCount, $expectedClass) {
			if (count($metrics) != $expectedCount) {
				return false;
			}
			for ($i = 0; $i < $expectedCount; $i++) {
				if (!($metrics[$i] instanceof $expectedClass)) {
					return false;
				}
			}
			return true;
		}))->andReturn(true)->once();

		$collector = new Collector();
		$collector->setBuffer($mockBuffer);
		$collector->$function($names, 10, 0.2);
	}

	/**
	 * @return m\MockInterface|AbstractMetric
	 */
	protected function mockMetric()
	{
		return m::mock('\Hitmeister\Component\Metrics\Metric\AbstractMetric');
	}
}