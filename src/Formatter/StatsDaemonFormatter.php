<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 10:38 PM
 */

namespace Hitmeister\Component\Metrics\Formatter;

use Hitmeister\Component\Metrics\Helper;
use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;

/**
 * Class StatsDaemonFormatter
 *
 * @package Hitmeister\Component\Metrics\Formatter
 */
class StatsDaemonFormatter extends Formatter
{
    /**
     * @inheritdoc
     */
    public function format(Metric $metric)
    {
        switch ($metric->getType()) {
            case 'counter':
            case 'memory':
                return $this->process($metric, 'c');
            case 'timer':
                return $this->process($metric, 'ms');
            case 'gauge':
                return $this->process($metric, 'g');
            case 'unique':
                return $this->process($metric, 's');
        }
        return false;
    }

    /**
     * Formats metric to stats daemon format
     * @see https://github.com/etsy/statsd/blob/master/docs/metric_types.md
     *
     * @param Metric $metric
     * @param string $daemonType
     * @return bool|string
     */
    protected function process(Metric &$metric, $daemonType)
    {
        // If no value, nothing to add
        $value = $metric->getValue();
        if (!isset($value['value'])) {
            return false;
        }

        $value = $value['value'];
        $name = $metric->getName();

        // Add tags to the beginning of the name
        if ($metric->hasTags()) {
            $name = Helper::mapAsString($metric->getTags()).'.'.$name;
        }

        // Name with value and type
        $result = $name.':'.$value.'|'.$daemonType;

        // Add sampling rate
        if ($metric instanceof SamplingMetricInterface) {
            if ($metric->getSampleRate() < 1.0) {
                $result .= '|@'.$metric->getSampleRate();
            }
        }

        return $result;
    }
}