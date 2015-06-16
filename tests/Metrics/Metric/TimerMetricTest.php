<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 11:37
 */

namespace Hitmeister\Component\Metrics\Tests\Metric;

use Hitmeister\Component\Metrics\Metric\TimerMetric;

class TimerMetricTest extends MetricTestCase
{
	/**
	 * @inheritdoc
	 */
	protected $className = '\Hitmeister\Component\Metrics\Metric\TimerMetric';

	/**
	 * @inheritdoc
	 */
	public function testType()
	{
		$metric = new TimerMetric('name', 1);
		$this->assertEquals('timer', $metric->getType());
	}
}