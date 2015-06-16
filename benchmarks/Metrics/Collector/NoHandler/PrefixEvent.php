<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 8:34 PM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\Collector\NoHandler;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Collector;

class PrefixEvent extends AthleticEvent
{
    /**
     * @var Collector
     */
    private $collector;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->collector = new Collector();
        $this->collector->setMetricPrefix('prefix_');
    }

    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        $this->collector = null;
    }

    /**
     * @iterations 1000
     */
    public function counterOnlyName()
    {
        $this->collector->counter('event_name', 1);
    }

    /**
     * @iterations 1000
     */
    public function counterOnlyNameMultiValue()
    {
        $this->collector->counter('event_name', ['internal' => 10, 'external' => 20]);
    }
}