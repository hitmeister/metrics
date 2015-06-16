<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:39
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class MemoryMetric
 *
 * It is recommended to use number of bytes as value!
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class MemoryMetric extends AbstractMetric
{
	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'memory';
	}
}