<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 9/11/15
 * Time: 11:31 PM
 */

namespace Hitmeister\Component\Metrics\Formatter\InfluxDb;

use Hitmeister\Component\Metrics\Formatter\Formatter;
use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;

/**
 * Class LineFormatter
 *
 * Warning! Please use this one for InfluxDb >= 0.9.3
 *
 * @package Hitmeister\Component\Metrics\Formatter\InfluxDb
 */
class LineFormatter extends Formatter
{
    /**
     * @inheritdoc
     */
    public function format(Metric $metric)
    {
        switch ($metric->getType()) {
            case 'gauge':
                return $this->processGauge($metric);
            default:
                return $this->process($metric);
        }
    }

    /**
     * Process the metric.
     *
     * @param Metric $metric
     * @return bool|string
     */
    protected function process(Metric &$metric)
    {
        // By the InfluxDb rules we have to have at least one value
        $value = $metric->getValue();
        if (empty($value)) {
            return false;
        }

        // Sampling
        if ($metric instanceof SamplingMetricInterface && $metric->getSampleRate() < 1) {
            foreach ($value as $k => $v) {
                if (!is_string($v))
                    $value[$k] = $v*$metric->getSampleRate();
            }
        }

        // Metric name
        $name = $this->quoteKey($metric->getName());

        // Metric tags
        if ($metric->hasTags()) {
            $name .= ','.$this->prepareTags($metric->getTags());
        }

        // Metric values
        $name .= ' '.$this->prepareValues($value);

        // Metric time (from milliseconds to nanoseconds)
        if ($metric->getTime()) {
            $name .= ' '.($metric->getTime() * 1000000);
        }

        return $name;
    }

    /**
     * Converts all gauge values to float
     *
     * @param Metric $metric
     * @return bool|string
     */
    protected function processGauge(Metric &$metric)
    {
        // All items should be int or float (currently values are: 10, +10, -10)
        $value = $metric->getValue();
        foreach ($value as $k => $v) {
            $value[$k] = (float)$v;
        }
        $metric->setValue($value);

        return $this->process($metric);
    }

    /**
     * Measurement names, tag keys, and tag values must escape any spaces or commas using a backslash.
     *
     * @param string $string
     * @return string
     */
    protected function quoteKey($string)
    {
        return preg_replace('/([\s,])/', '\\\${1}', $string);
    }

    /**
     * @param array $tags
     * @return string
     */
    protected function prepareTags(array $tags)
    {
        ksort($tags);
        $pairs = [];
        foreach($tags as $key => $value) {
            $pairs[] = $this->quoteKey($key).'='.$this->quoteKey($value);
        }
        return implode(',', $pairs);
    }

    /**
     * @param array $values
     * @return string
     */
    protected function prepareValues(array $values)
    {
        $pairs = [];
        foreach($values as $key => $value) {
            switch (true) {
                case is_float($value):
                    $value = (float)$value;
                    break;
                case is_int($value):
                    $value = $value.'i';
                    break;
                case is_bool($value):
                    $value = ($value?'true':'false');
                    break;
                default:
                    $value = '"'.str_replace('"', '\"', $value).'"';
                    break;
            }
            $pairs[] = $this->quoteKey($key).'='.$value;
        }
        return implode(',', $pairs);
    }
}