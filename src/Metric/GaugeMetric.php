<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:41
 */

namespace Hitmeister\Component\Metrics\Metric;

use Hitmeister\Component\Metrics\Metric;

/**
 * Class GaugeMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class GaugeMetric extends Metric
{
	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'gauge';
	}
}