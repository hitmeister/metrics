<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 13:48
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\Metric;
use Psr\Log\LoggerInterface;

abstract class Buffer implements BufferInterface
{
	/**
	 * Logger interface.
	 * If set it will log only errors.
	 *
	 * @var LoggerInterface
	 */
	protected $logger;

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
	 * @codeCoverageIgnore
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

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
        $now = (int)(microtime(true) * 1000);
		foreach ($metrics as $metric) {
			if ($metric instanceof Metric) {
				$metric->setTime($now);
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