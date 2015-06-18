<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:48
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Metric\Metric;

class ImmediateBuffer extends Buffer
{
	/**
	 * @inheritdoc
	 */
	public function add(Metric $metric)
	{
		if ($this->handler) {
			try {
				return $this->handler->handle($metric);
			} catch (\Exception $e) {
				// @codeCoverageIgnoreStart
				if ($this->logger) {
					$this->logger->error('An error occurred while processing metric', [
						'exception' => $e,
						'metric' => $metric,
					]);
				}
				// @codeCoverageIgnoreEnd
			}
		}
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function addBatch(array $metrics)
	{
		if ($this->handler) {
			try {
				return $this->handler->handleBatch($metrics);
			} catch (\Exception $e) {
				// @codeCoverageIgnoreStart
				if ($this->logger) {
					$this->logger->error('An error occurred while processing batch of metrics', [
						'exception' => $e,
						'batch_size' => count($metrics),
					]);
				}
				// @codeCoverageIgnoreEnd
			}
		}
		return false;
	}
}