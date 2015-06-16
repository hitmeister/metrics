<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:41
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class UniqueMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class UniqueMetric extends AbstractMetric
{
	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'unique';
	}
}