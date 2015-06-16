<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 11:37
 */

namespace Hitmeister\Component\Metrics\Tests\Metric;

use Hitmeister\Component\Metrics\Metric\MemoryMetric;

class MemoryMetricTest extends MetricTestCase
{
	/**
	 * @inheritdoc
	 */
	protected $className = '\Hitmeister\Component\Metrics\Metric\MemoryMetric';

	/**
	 * @inheritdoc
	 */
	public function testType()
	{
		$metric = new MemoryMetric('name', 1);
		$this->assertEquals('memory', $metric->getType());
	}
}