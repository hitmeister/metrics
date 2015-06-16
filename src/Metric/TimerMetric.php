<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:39
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class TimerMetric
 *
 * It is recommended to use number of milliseconds as value!
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class TimerMetric extends SamplingMetric
{
	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'timer';
	}
}