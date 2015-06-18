<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/18/15
 * Time: 8:23 PM
 */

namespace Hitmeister\Component\Metrics\Collector;

use Hitmeister\Component\Metrics\Buffer\BufferInterface;
use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;
use Symfony\Component\Stopwatch\Stopwatch;

abstract class AbstractCollector implements CollectorInterface
{
    /**
     * Prefix for metrics.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Tags will be added to each metric.
     * For example: env or server name.
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Buffer for metrics.
     *
     * @var BufferInterface
     */
    protected $buffer;

    /**
     * @inheritdoc
     */
    public function increment($names, array $tags = [], $value = 1, $sampleRate = 1.0)
    {
        return $this->counter($names, $value, $tags, $sampleRate);
    }

    /**
     * @inheritdoc
     */
    public function decrement($names, array $tags = [], $value = 1, $sampleRate = 1.0)
    {
        return $this->counter($names, ($value < 0 ? $value : -$value), $tags, $sampleRate);
    }

    /**
     * @inheritdoc
     */
    public function counter($names, $value, array $tags = [], $sampleRate = 1.0)
    {
        $names = (array)$names;
        $className = '\Hitmeister\Component\Metrics\Metric\CounterMetric';
        $metrics = $this->createMetrics($className, $names, $value, $tags, $sampleRate);
        $this->buffer->addBatch($metrics);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function timer($names, $value, array $tags = [], $sampleRate = 1.0)
    {
        $names = (array)$names;
        $className = '\Hitmeister\Component\Metrics\Metric\TimerMetric';
        $metrics = $this->createMetrics($className, $names, $value, $tags, $sampleRate);
        $this->buffer->addBatch($metrics);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function memory($names, $value, array $tags = [], $sampleRate = 1.0)
    {
        $names = (array)$names;
        $className = '\Hitmeister\Component\Metrics\Metric\MemoryMetric';
        $metrics = $this->createMetrics($className, $names, $value, $tags, $sampleRate);
        $this->buffer->addBatch($metrics);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function gauge($names, $value, array $tags = [])
    {
        $names = (array)$names;
        $className = '\Hitmeister\Component\Metrics\Metric\GaugeMetric';
        $metrics = $this->createMetrics($className, $names, $value, $tags);
        $this->buffer->addBatch($metrics);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unique($names, $value, array $tags = [])
    {
        $names = (array)$names;
        $className = '\Hitmeister\Component\Metrics\Metric\UniqueMetric';
        $metrics = $this->createMetrics($className, $names, $value, $tags);
        $this->buffer->addBatch($metrics);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function closure($name, $function, array $tags = [], $sampleRate = 1.0)
    {
        if (!is_callable($function)) {
            throw new \LogicException('$function argument is not callable');
        }

        // Event happened once
        $this->increment($name, $tags, 1, $sampleRate);

        // Start trackers
        $stopwatch = new Stopwatch();
        $stopwatch->start($name);

        try {
            // Run
            $function();
        } catch (\Exception $e) {
            // Track exception
            $this->increment($name.'_exception', $tags);

            // Do not hide the exception
            throw $e;
        } finally {
            $event = $stopwatch->stop($name);

            // Report
            $this->timer($name.'_time', $event->getDuration(), $tags, $sampleRate);
            $this->memory($name.'_memory', $event->getMemory(), $tags, $sampleRate);
        }

        return $this;
    }

    /**
     * @param string $className
     * @param string $name
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     * @return Metric
     */
    protected function createMetric($className, &$name, &$value, array &$tags = [], &$sampleRate = 1.0)
    {
        /** @var Metric|SamplingMetricInterface $metric */
        $metric = new $className(($this->prefix ? $this->prefix.$name : $name), $value, $this->tags);

        // Specific tags
        if (!empty($tags)) {
            $metric->addTags($tags);
        }

        // Sampling
        if ($sampleRate < 1 && ($metric instanceof SamplingMetricInterface)) {
            $metric->setSampleRate($sampleRate);
        }

        return $metric;
    }

    /**
     * @param string $className
     * @param array  $names
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     * @return Metric[]
     */
    protected function createMetrics($className, array &$names, &$value, array &$tags = [], &$sampleRate = 1.0)
    {
        $metrics = [];
        foreach ($names as $name) {
            $metrics[] = $this->createMetric($className, $name, $value, $tags, $sampleRate);
        }
        return $metrics;
    }
}