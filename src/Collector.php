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
use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;

class Collector
{
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
	 * Increments one or more metrics to value points.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param array        $tags
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function increment($names, $value = 1, array $tags = [], $sampleRate = 1.0)
	{
		return $this->counter($names, $value, $tags, $sampleRate);
	}

	/**
	 * Decrements one or more metrics to value points.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param array        $tags
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function decrement($names, $value = 1, array $tags = [], $sampleRate = 1.0)
	{
		return $this->counter($names, ($value < 0 ? $value : -$value), $tags, $sampleRate);
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param mixed        $value
	 * @param array        $tags
	 * @param float        $sampleRate
	 * @return $this
	 */
	public function counter($names, $value, array $tags = [], $sampleRate = 1.0)
	{
        $metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\CounterMetric', $names, $value, $tags, $sampleRate);
		$this->buffer->addBatch($metrics);
        return $this;
	}

	/**
	 * Counts one or more metrics.
	 * It is recommended to use number of milliseconds as value!
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param array        $tags
     * @param float        $sampleRate
	 * @return $this
	 */
	public function timer($names, $value, array $tags = [], $sampleRate = 1.0)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\TimerMetric', $names, $value, $tags, $sampleRate);
		$this->buffer->addBatch($metrics);
		return $this;
	}

	/**
	 * Counts one or more metrics.
	 * It is recommended to use number of bytes as value!
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param array        $tags
     * @param float        $sampleRate
	 * @return $this
	 */
	public function memory($names, $value, array $tags = [], $sampleRate = 1.0)
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\MemoryMetric', $names, $value, $tags, $sampleRate);
		$this->buffer->addBatch($metrics);
		return $this;
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param int          $value
	 * @param array        $tags
	 * @return $this
	 */
	public function gauge($names, $value, array $tags = [])
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\GaugeMetric', $names, $value, $tags);
		$this->buffer->addBatch($metrics);
		return $this;
	}

	/**
	 * Counts one or more metrics.
	 *
	 * @param string|array $names
	 * @param mixed        $value
	 * @param array        $tags
	 * @return $this
	 */
	public function unique($names, $value, array $tags = [])
	{
		$metrics = $this->createMetrics('\Hitmeister\Component\Metrics\Metric\UniqueMetric', $names, $value, $tags);
		$this->buffer->addBatch($metrics);
		return $this;
	}

	/**
	 * @param string       $className
	 * @param string|array $names
	 * @param mixed        $value
	 * @param array        $tags
     * @param float        $sampleRate
	 * @return array
	 */
	protected function createMetrics($className, &$names, &$value, array &$tags = [], &$sampleRate = 1.0)
	{
		$metrics = [];
		foreach ((array)$names as $name) {
			/** @var Metric|SamplingMetricInterface $metric */
			$metric = new $className($this->metricPrefix.$name, $value, $this->tags);
			if (!empty($tags)) {
				$metric->addTags($tags);
			}
            if (1.0 != $sampleRate && $metric instanceof SamplingMetricInterface) {
                $metric->setSampleRate($sampleRate);
            }
			$metrics[] = $metric;
		}
		return $metrics;
	}
}