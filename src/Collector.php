<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/13/15
 * Time: 10:26 PM
 */

namespace Hitmeister\Component\Metrics;

use Hitmeister\Component\Metrics\Buffer\BufferInterface;
use Hitmeister\Component\Metrics\Buffer\ImmediateBuffer;
use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\AbstractMetric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;
use Psr\Log\LoggerInterface;

class Collector
{
	/**
	 * Logger interface.
	 * If set it will log only errors.
	 *
	 * @var LoggerInterface
	 */
	private $logger;

	/**
	 * Prefix for metrics.
	 *
	 * @var string
	 */
	private $metricPrefix = '';

	/**
	 * Tags will be added to each metric.
	 * For example: env or server name.
	 *
	 * @var array
	 */
	private $tags = [];

	/**
	 * Buffer for metrics.
	 *
	 * @var BufferInterface
	 */
	private $buffer;

	/**
	 * Creates new instance of Collector
	 */
	public function __construct()
	{
		$this->buffer = new ImmediateBuffer();
	}

	/**
	 * Returns logger.
	 *
	 * @return LoggerInterface
	 * @codeCoverageIgnore
	 */
	public function getLogger()
	{
		return $this->logger;
	}

	/**
	 * Sets logger.
	 *
	 * @param LoggerInterface $logger
	 * @return $this
	 * @codeCoverageIgnore
	 */
	public function setLogger(LoggerInterface $logger)
	{
		$this->logger = $logger;
		return $this;
	}

	/**
	 * Returns metric prefix.
	 *
	 * @return string
	 */
	public function getMetricPrefix()
	{
		return $this->metricPrefix;
	}

	/**
	 * Sets metric prefix.
	 *
	 * @param string $metricPrefix
	 * @return $this
	 */
	public function setMetricPrefix($metricPrefix)
	{
		$this->metricPrefix = (string)$metricPrefix;
		return $this;
	}

	/**
	 * Returns true if collector hs tags
	 *
	 * @return bool
	 */
	public function hasTags()
	{
		return !empty($this->tags);
	}

	/**
	 * Returns tags.
	 *
	 * @return array
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Sets tags.
	 *
	 * @param array $tags
	 * @return $this
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
		return $this;
	}

	/**
	 * Adds tag.
	 *
	 * @param string $key
	 * @param string $value
	 * @return $this
	 */
	public function addTag($key, $value)
	{
		$this->tags[$key] = $value;
		return $this;
	}

	/**
	 * Removes tag.
	 *
	 * @param string $key
	 * @return $this
	 */
	public function removeTag($key)
	{
		if (isset($this->tags[$key])) {
			unset($this->tags[$key]);
		}
		return $this;
	}

	/**
	 * Returns buffer.
	 *
	 * @return BufferInterface
	 */
	public function getBuffer()
	{
		return $this->buffer;
	}

	/**
	 * Sets buffer.
	 *
	 * @param BufferInterface $buffer
	 * @return $this
	 */
	public function setBuffer(BufferInterface $buffer)
	{
		$this->buffer = $buffer;
		return $this;
	}

	/**
	 * Sets handler to buffer.
	 *
	 * @param HandlerInterface $handler
	 * @return $this
	 */
	public function setHandler(HandlerInterface $handler)
	{
		$this->buffer->setHandler($handler);
		return $this;
	}

	/**
	 * Adds metric into the buffer
	 *
	 * @param AbstractMetric $metric
	 * @return $this
	 */
	public function buffer(AbstractMetric $metric)
	{
		try {
			$this->buffer->add($metric);
		} catch (\Exception $e) {
			// @codeCoverageIgnoreStart
			if ($this->logger) {
				$this->logger->error('Unable to save metric into the buffer', [
					'exception' => $e,
					'metric' => $metric,
				]);
			}
			// @codeCoverageIgnoreEnd
		}
		return $this;
	}

	/**
	 * Adds batch of metrics into the buffer
	 *
	 * @param AbstractMetric[] $metrics
	 * @return $this
	 */
	public function bufferBatch(array $metrics)
	{
		if (empty($metrics)) {
			return $this;
		}

		try {
			$this->buffer->addBatch($metrics);
		} catch (\Exception $e) {
			// @codeCoverageIgnoreStart
			if ($this->logger) {
				$this->logger->error('Unable to save metric into the buffer', [
					'exception' => $e,
					'batch_size' => count($metrics),
				]);
			}
			// @codeCoverageIgnoreEnd
		}
		return $this;
	}

	/**
	 * Increments one or more metrics to value points.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function increment($names, $value = 1, $sampleRate = 1.0)
	{
		return $this->counter($names, $value, $sampleRate);
	}

	/**
	 * Decrements one or more metrics to value points.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function decrement($names, $value = 1, $sampleRate = 1.0)
	{
		return $this->counter($names, ($value < 0 ? $value : -$value), $sampleRate);
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param mixed        $value
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function counter($names, $value, $sampleRate = 1.0)
	{
        $metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\CounterMetric', $names, $value, $sampleRate);
        return $this->bufferBatch($metrics);
	}

	/**
	 * Counts one or more metrics.
	 * It is recommended to use number of milliseconds as value!
	 *
	 * @param string|array $names
	 * @param int          $value
     * @param float        $sampleRate
	 * @return $this
	 */
	public function timer($names, $value, $sampleRate = 1.0)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\TimerMetric', $names, $value, $sampleRate);
		return $this->bufferBatch($metrics);
	}

	/**
	 * Counts one or more metrics.
	 * It is recommended to use number of bytes as value!
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @return $this
	 */
	public function memory($names, $value)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\MemoryMetric', $names, $value);
		return $this->bufferBatch($metrics);
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @return $this
	 */
	public function gauge($names, $value)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\GaugeMetric', $names, $value);
		return $this->bufferBatch($metrics);
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param mixed        $value
	 * @return $this
	 */
	public function unique($names, $value)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\UniqueMetric', $names, $value);
		return $this->bufferBatch($metrics);
	}

	/**
	 * @param string       $className
	 * @param string|array $names
	 * @param mixed        $value
     * @param float        $sampleRate
	 * @return array
	 */
	protected function createMetrics($className, $names, $value, $sampleRate = 1.0)
	{
		$metrics = [];
		foreach ((array)$names as $name) {
			$metric = new $className($this->metricPrefix.$name, $value, $this->tags);
            if (1.0 != $sampleRate && $metric instanceof SamplingMetricInterface) {
                $metric->setSampleRate($sampleRate);
            }
			$metrics[] = $metric;
		}
		return $metrics;
	}
}