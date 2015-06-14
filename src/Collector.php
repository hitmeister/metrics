<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/13/15
 * Time: 10:26 PM
 */

namespace Hitmeister\Component\Metrics;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;

class Collector
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @var array
     */
    private $globalTags = [];

    /**
     * Do not throw any Exceptions on write operations
     * @var bool
     */
    private $silentOnWrite = true;

    /**
     * @var bool
     */
    private $flushOnShutdown = false;

    /**
     * @var bool
     */
    private $shutdownRegistered = false;

    /**
     * @var array
     */
    private $metrics = [];

    /**
     * @var array
     */
    private $timers = [];

    /**
     * @var array
     */
    private $memory = [];

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return HandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @return bool
     */
    public function hasGlobalTags()
    {
        return !empty($this->globalTags);
    }

    /**
     * @return array
     */
    public function getGlobalTags()
    {
        return $this->globalTags;
    }

    /**
     * @param array $tags
     * @return Collector
     */
    public function setGlobalTags(array $tags)
    {
        $this->globalTags = $tags;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Collector
     */
    public function addGlobalTag($key, $value)
    {
        $this->globalTags[$key] = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return Collector
     */
    public function removeGlobalTag($key)
    {
        if (isset($this->globalTags[$key])) {
            unset($this->globalTags[$key]);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isWriteSilent()
    {
        return $this->silentOnWrite;
    }

    /**
     * @param bool $value
     * @return Collector
     */
    public function setWriteSilent($value)
    {
        $this->silentOnWrite = (bool)$value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFlushOnShutdown()
    {
        return $this->flushOnShutdown;
    }

    /**
     * @param $value
     * @return Collector
     */
    public function setFlushOnShutdown($value)
    {
        $this->flushOnShutdown = (bool)$value;
        if ($this->flushOnShutdown) {
            if (!$this->shutdownRegistered) {
                register_shutdown_function(array($this, 'flush'));
                $this->shutdownRegistered = true;
            }
        }
        return $this;
    }

    /**
     * @param string $name
     * @param int $value
     * @param array $tags
     * @param float $sampleRate
     * @return Collector
     */
    public function increment($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        return $this->count($name, $value, $tags, $sampleRate);
    }

    /**
     * @param string $name
     * @param int $value
     * @param array $tags
     * @param float $sampleRate
     * @return Collector
     */
    public function decrement($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        return $this->count($name, -$value, $tags, $sampleRate);
    }

    /**
     * @param string $name
     * @param int $value
     * @param array $tags
     * @param float $sampleRate
     * @throws \Exception
     * @return Collector
     */
    public function count($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $metric = new Metric($name, $value, Metric::TYPE_COUNT, $this->globalTags);
        $metric->setSampleRate($sampleRate)->addTags($tags);

        $this->handle($metric);
        return $this;
    }

    /**
     * @param string $name
     * @param int|float $value
     * @param array $tags
     * @throws \Exception
     * @return Collector
     */
    public function time($name, $value, array $tags = [])
    {
        $metric = new Metric($name, $value, Metric::TYPE_TIME, $this->globalTags);
        $metric->addTags($tags);

        $this->handle($metric);
        return $this;
    }

    /**
     * @param string $name
     * @return Collector
     */
    public function startTimer($name)
    {
        // milliseconds
        $this->timers[$name] = microtime(true) * 1000;
        return $this;
    }

    /**
     * Stops timer and returns spent time is milliseconds
     *
     * @param string $name
     * @return int
     */
    public function stopTimer($name)
    {
        if (!isset($this->timers[$name])) {
            return 0;
        }
        $time = (microtime(true) * 1000) - $this->timers[$name];
        unset($this->timers[$name]);
        return (int)($time);
    }

    /**
     * @param string $name
     * @param array $tags
     * @return Collector
     */
    public function reportTimer($name, array $tags = [])
    {
        return $this->time($name, $this->stopTimer($name), $tags);
    }

    /**
     * @param string $name
     * @param int $value
     * @param array $tags
     * @return Collector
     * @throws \Exception
     */
    public function gauge($name, $value, array $tags = [])
    {
        $metric = new Metric($name, $value, Metric::TYPE_GAUGE, $this->globalTags);
        $metric->addTags($tags);

        $this->handle($metric);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @return Collector
     * @throws \Exception
     */
    public function set($name, $value, array $tags = [])
    {
        $metric = new Metric($name, $value, Metric::TYPE_SET, $this->globalTags);
        $metric->addTags($tags);

        $this->handle($metric);
        return $this;
    }

    /**
     * @param string $name
     * @return Collector
     */
    public function startMemory($name)
    {
        $this->memory[$name] = memory_get_usage(true);
        return $this;
    }

    /**
     * Stops memory tracker and returns memory difference in bytes
     *
     * @param string $name
     * @return int
     */
    public function stopMemory($name)
    {
        if (!isset($this->memory[$name])) {
            return 0;
        }
        $memory = memory_get_usage(true) - $this->memory[$name];
        unset($this->memory[$name]);
        return $memory;
    }

    /**
     * @param string $name
     * @param array $tags
     * @return Collector
     */
    public function reportMemory($name, array $tags = [])
    {
        return $this->count($name, $this->stopMemory($name), $tags);
    }

    /**
     * @param Metric $metric
     * @throws \Exception
     */
    protected function handle(Metric $metric)
    {
        if ($this->flushOnShutdown) {
            $metric->setTime(microtime(true)/*, Metric::PRECISION_MICROSECONDS*/);
            $this->metrics[] = $metric;
        } else {
            try {
                $this->handler->handle($metric);
            } catch (\Exception $e) {
                if (!$this->silentOnWrite) {
                    throw $e;
                }
            }
        }
    }

    /**
     * @return Collector
     * @throws \Exception
     */
    public function flush()
    {
        if (empty($this->metrics)) {
            return $this;
        }
        try {
            $this->handler->handleBatch($this->metrics);
        } catch (\Exception $e) {
            if (!$this->silentOnWrite) {
                throw $e;
            }
        }
        return $this;
    }
}