<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:48
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric;

abstract class Buffer implements BufferInterface
{
	/**
	 * Handler.
	 *
	 * @var HandlerInterface
	 */
	protected $handler;

	/**
	 * Batch of metrics in buffer.
	 *
	 * @var Metric[]
	 */
	protected $metrics = [];

	/**
	 * @inheritdoc
	 */
	public function setHandler(HandlerInterface $handler)
	{
		$this->handler = $handler;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function add(Metric $metric)
	{
		$metric->touch();
		$this->metrics[] = $metric;
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function addBatch(array $metrics)
	{
		foreach ($metrics as $metric) {
			if ($metric instanceof Metric) {
				$metric->touch();
				$this->metrics[] = $metric;
			}
		}
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function getData()
	{
		return $this->metrics;
	}

	/**
	 * @inheritdoc
	 */
	public function flush()
	{
		$this->metrics = [];
		return true;
	}
}