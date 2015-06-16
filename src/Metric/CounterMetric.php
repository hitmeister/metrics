<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:31
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class CounterMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class CounterMetric extends SamplingMetric
{
	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'counter';
	}
}