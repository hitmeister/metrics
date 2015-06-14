<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/14/15
 * Time: 11:08 PM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;

class ShutdownFlush2Event extends AthleticEvent
{
    /**
     * @var Collector
     */
    private $collector;

    /**
     * @var StatsDaemonHandler
     */
    private $handler;

    protected function classSetUp()
    {
        $this->handler = new StatsDaemonHandler();
        $this->collector = new Collector($this->handler);
        $this->collector->setFlushOnShutdown(true);
        $this->collector->setGlobalTags(['glob1' => 'value1']);
    }

    protected function classTearDown()
    {
        $this->collector->flush();
        unset($this->handler, $this->collector);
    }

    /**
     * @iterations 1000
     */
    public function nameCounterWithGlobTags()
    {
        $this->collector->count('name', 1);
    }

    /**
     * @iterations 1000
     */
    public function nameSampleRateCounterWithGlobTags()
    {
        $this->collector->count('name', 1, [], 0.2);
    }

    /**
     * @iterations 1000
     */
    public function nameOneTagCounterWithGlobTags()
    {
        $this->collector->count('name', 1, ['evn' => 'dev']);
    }

    /**
     * @iterations 1000
     */
    public function nameFiveTagsCounterWithGlobTags()
    {
        $this->collector->count('name', 1, ['tag1' => 'val1', 'tag2' => 'val2', 'tag3' => 'val3', 'tag4' => 'val4', 'tag5' => 'val5']);
    }
}