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
		$flushed = false;

		if ($this->handler) {
			try {
				$flushed = $this->handler->handleBatch($this->metrics);
			} catch (\Exception $e) {
				// @codeCoverageIgnoreStart
				if ($this->logger) {
					$this->logger->error('An error occurred while flush batch of metrics', [
						'exception' => $e,
						'batch_size' => count($this->metrics),
					]);
				}
				// @codeCoverageIgnoreEnd
			}
		}

		return $flushed && parent::flush();
	}
}