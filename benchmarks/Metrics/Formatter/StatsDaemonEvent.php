<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 11:46 PM
 */

namespace Hitmeister\Component\Metrics\Benchmarks\Formatter;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Formatter\StatsDaemonFormatter;
use Hitmeister\Component\Metrics\Metric\CounterMetric;

class StatsDaemonEvent extends AthleticEvent
{
    /**
     * @var StatsDaemonFormatter
     */
    private $formatter;

    /**
     * @var CounterMetric
     */
    private $metricName;

    /**
     * @var CounterMetric
     */
    private $metricTags;

    /**
     * @var CounterMetric
     */
    private $metricTagsSample;

    /**
     * @inheritdoc
     */
    protected function classSetUp()
    {
        $this->formatter = new StatsDaemonFormatter();

        $this->metricName = new CounterMetric('metric_name', 10);

        $this->metricTags = new CounterMetric('metric_name', 10, ['env' => 'prod', 'server' => 'web01']);

        $this->metricTagsSample = new CounterMetric('metric_name', 10, ['env' => 'prod', 'server' => 'web01']);
        $this->metricTagsSample->setSampleRate(0.4);
    }

    /**
     * @iterations 10000
     */
    public function counterName()
    {
        $this->formatter->format($this->metricName);
    }

    /**
     * @iterations 1000
     */
    public function counterNameAndTags()
    {
        $this->formatter->format($this->metricTags);
    }

    /**
     * @iterations 1000
     */
    public function counterNameTagsAndSample()
    {
        $this->formatter->format($this->metricTagsSample);
    }
}