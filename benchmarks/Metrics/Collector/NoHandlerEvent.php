<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 8:34 PM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\Collector;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Collector;

class NoHandlerEvent extends AthleticEvent
{
    /**
     * @var Collector
     */
    private $collectorBase;

    /**
     * @var Collector
     */
    private $collectorPrefix;

    /**
     * @var Collector
     */
    private $collectorTags;

    /**
     * @var Collector
     */
    private $collectorTagsPrefix;

    /**
     * @inheritdoc
     */
    protected function classSetUp()
    {
        $this->collectorBase = new Collector();

        $this->collectorPrefix = new Collector();
        $this->collectorPrefix->setMetricPrefix('prefix_');

        $this->collectorTags = new Collector();
        $this->collectorTags->setTags(['env' => 'prod', 'server' => 'web01']);

        $this->collectorTagsPrefix = new Collector();
        $this->collectorTagsPrefix->setTags(['env' => 'prod', 'server' => 'web01']);
        $this->collectorTagsPrefix->setMetricPrefix('prefix_');
    }

    /**
     * @iterations 1000
     */
    public function counterName()
    {
        $this->collectorBase->counter('event_name', 1);
    }

    /**
     * @iterations 1000
     */
    public function counterNameMultiValue()
    {
        $this->collectorBase->counter('event_name', ['internal' => 10, 'external' => 20]);
    }

    /**
     * @iterations 1000
     */
    public function counterPrefixName()
    {
        $this->collectorPrefix->counter('event_name', 1);
    }

    /**
     * @iterations 1000
     */
    public function counterPrefixNameMultiValue()
    {
        $this->collectorPrefix->counter('event_name', ['internal' => 10, 'external' => 20]);
    }

    /**
     * @iterations 1000
     */
    public function counterTagsName()
    {
        $this->collectorTags->counter('event_name', 1);
    }

    /**
     * @iterations 1000
     */
    public function counterTagsNameMultiValue()
    {
        $this->collectorTags->counter('event_name', ['internal' => 10, 'external' => 20]);
    }

    /**
     * @iterations 1000
     */
    public function counterTagsNamePrefix()
    {
        $this->collectorTagsPrefix->counter('event_name', 1);
    }

    /**
     * @iterations 1000
     */
    public function counterTagsNamePrefixMultiValue()
    {
        $this->collectorTagsPrefix->counter('event_name', ['internal' => 10, 'external' => 20]);
    }
}