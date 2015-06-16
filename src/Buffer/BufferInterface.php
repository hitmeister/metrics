<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:42
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\AbstractMetric;

interface BufferInterface
{
	/**
	 * Sets handler.
	 *
	 * @param HandlerInterface $handler
	 * @return $this
	 */
	public function setHandler(HandlerInterface $handler);

	/**
	 * Adds one metric to buffer.
	 *
	 * @param AbstractMetric $metric
	 * @return bool
	 */
	public function add(AbstractMetric $metric);

	/**
	 * Adds batch of metrics to buffer.
	 *
	 * @param AbstractMetric[] $metrics
	 * @return bool
	 */
	public function addBatch(array $metrics);

	/**
	 * Returns batch of metrics.
	 *
	 * @return AbstractMetric[]
	 */
	public function getData();

	/**
	 * Flush data.
	 *
	 * @return bool
	 */
	public function flush();
}