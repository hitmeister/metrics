<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/17/15
 * Time: 1:14 AM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\Collector;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Buffer\OnShutdownBuffer;
use Hitmeister\Component\Metrics\Collector;
use Hitmeister\Component\Metrics\Handler\StatsDaemonHandler;

class StatsDaemonShutdownEvent extends AthleticEvent
{
    /**
     * @var Collector
     */
    protected $collectorBase;

    /**
     * @var Collector
     */
    protected $collectorPrefix;

    /**
     * @var Collector
     */
    protected $collectorTags;

    /**
     * @var Collector
     */
    protected $collectorTagsPrefix;

    /**
     * @inheritdoc
     */
    protected function classSetUp()
    {
        $this->collectorBase = new Collector();
        $this->collectorBase->setBuffer(new OnShutdownBuffer());
        $this->collectorBase->setHandler(new StatsDaemonHandler());

        $this->collectorPrefix = new Collector();
        $this->collectorPrefix->setBuffer(new OnShutdownBuffer());
        $this->collectorPrefix->setHandler(new StatsDaemonHandler());
        $this->collectorPrefix->setPrefix('prefix_');

        $this->collectorTags = new Collector();
        $this->collectorTags->setBuffer(new OnShutdownBuffer());
        $this->collectorTags->setHandler(new StatsDaemonHandler());
        $this->collectorTags->setTags(['env' => 'prod', 'server' => 'web01']);

        $this->collectorTagsPrefix = new Collector();
        $this->collectorTagsPrefix->setBuffer(new OnShutdownBuffer());
        $this->collectorTagsPrefix->setHandler(new StatsDaemonHandler());
        $this->collectorTagsPrefix->setTags(['env' => 'prod', 'server' => 'web01']);
        $this->collectorTagsPrefix->setPrefix('prefix_');
    }

    /**
     * @inheritdoc
     */
    protected function classTearDown()
    {
        $this->collectorBase = null;
        $this->collectorPrefix = null;
        $this->collectorTags = null;
        $this->collectorTagsPrefix = null;
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