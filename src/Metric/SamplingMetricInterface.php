<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/16/15
 * Time: 9:52 PM
 */

namespace Hitmeister\Component\Metrics\Metric;

interface SamplingMetricInterface
{
    /**
     * Returns sample rate for value
     *
     * @return float
     */
    public function getSampleRate();

    /**
     * Sets sample rate for metric
     *
     * @param float $sampleRate
     * @return $this
     */
    public function setSampleRate($sampleRate);
}