<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 11:08 PM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\MetricToStatsDaemon;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;

class ShutdownFlush1Event extends AthleticEvent
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
    }

    protected function classTearDown()
    {
        $this->collector->flush();
        unset($this->handler, $this->collector);
    }

    /**
     * @iterations 1000
     */
    public function nameCounter()
    {
        $this->collector->count('name', 1);
    }

    /**
     * @iterations 1000
     */
    public function nameSampleRateCounter()
    {
        $this->collector->count('name', 1, [], 0.2);
    }

    /**
     * @iterations 1000
     */
    public function nameOneTagCounter()
    {
        $this->collector->count('name', 1, ['evn' => 'dev']);
    }

    /**
     * @iterations 1000
     */
    public function nameFiveTagsCounter()
    {
        $this->collector->count('name', 1, ['tag1' => 'val1', 'tag2' => 'val2', 'tag3' => 'val3', 'tag4' => 'val4', 'tag5' => 'val5']);
    }
}