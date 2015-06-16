<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:48
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Metric\AbstractMetric;

class ImmediateBuffer extends Buffer
{
	/**
	 * @inheritdoc
	 */
	public function add(AbstractMetric $metric)
	{
		if ($this->handler) {
			return $this->handler->handle($metric);
		}
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function addBatch(array $metrics)
	{
		if ($this->handler) {
			return $this->handler->handleBatch($metrics);
		}
		return false;
	}
}