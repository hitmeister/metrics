<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 14:48
 */

namespace Hitmeister\Component\Metrics\Buffer;

class ManualBuffer extends Buffer
{
	/**
	 * @inheritdoc
	 */
	public function flush()
	{
		$flushed = !$this->handler ? false : $this->handler->handleBatch($this->metrics);
		return $flushed && parent::flush();
	}
}