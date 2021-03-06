<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:42
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\Metric;
use Psr\Log\LoggerInterface;

interface BufferInterface
{
	/**
	 * Returns logger.
	 * 
	 * @return LoggerInterface
	 */
	public function getLogger();

	/**
	 * Sets logger.
	 *
	 * @param LoggerInterface $logger
	 * @return $this
	 */
	public function setLogger(LoggerInterface $logger);

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
	 * @param Metric $metric
	 * @return bool
	 */
	public function add(Metric $metric);

	/**
	 * Adds batch of metrics to buffer.
	 *
	 * @param Metric[] $metrics
	 * @return bool
	 */
	public function addBatch(array $metrics);

	/**
	 * Returns batch of metrics.
	 *
	 * @return Metric[]
	 */
	public function getData();

	/**
	 * Flush data.
	 *
	 * @return bool
	 */
	public function flush();
}