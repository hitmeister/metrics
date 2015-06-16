<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 11:28
 */

namespace Hitmeister\Component\Metrics\Tests\Metric;

use Hitmeister\Component\Metrics\Metric\CounterMetric;

class CounterMetricTest extends MetricTestCase
{
	/**
	 * @inheritdoc
	 */
	protected $className = '\Hitmeister\Component\Metrics\Metric\CounterMetric';

	/**
	 * @inheritdoc
	 */
	public function testType()
	{
		$metric = new CounterMetric('name', 1);
		$this->assertEquals('counter', $metric->getType());
	}

	public function testSampleRate()
	{
		$metric = new CounterMetric('name', 1);
		$this->assertEquals(1.0, $metric->getSampleRate());
		$metric->setSampleRate(0.2);
		$this->assertEquals(0.2, $metric->getSampleRate());
		$metric->setSampleRate(10);
		$this->assertEquals(1.0, $metric->getSampleRate());
		$metric->setSampleRate(-10);
		$this->assertEquals(0.0, $metric->getSampleRate());
	}
}