<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 9:54 PM
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class SamplingMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
abstract class SamplingMetric extends Metric implements SamplingMetricInterface
{
    /**
     * @var float
     */
    private $sampleRate = 1.0;

    /**
     * Returns sample rate for value
     *
     * @return float
     */
    public function getSampleRate()
    {
        return $this->sampleRate;
    }

    /**
     * Sets sample rate for metric
     *
     * @param float $sampleRate
     * @return $this
     */
    public function setSampleRate($sampleRate)
    {
        if (1.0 != $sampleRate) {
            $sampleRate = min(1.0, max(0.0, $sampleRate));
        }
        $this->sampleRate = $sampleRate;
        return $this;
    }
}