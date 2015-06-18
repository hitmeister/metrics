<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/18/15
 * Time: 7:40 PM
 */

namespace Hitmeister\Component\Metrics\Collector;

interface CollectorInterface
{
    /**
     * Increments one or more metrics to value points.
     *
     * @param string|array $names
     * @param array        $tags
     * @param int          $value
     * @param float        $sampleRate
     * @return $this
     */
    public function increment($names, array $tags = [], $value = 1, $sampleRate = 1.0);

    /**
     * Decrements one or more metrics to value points.
     *
     * @param string|array $names
     * @param array        $tags
     * @param int          $value
     * @param float        $sampleRate
     * @return $this
     */
    public function decrement($names, array $tags = [], $value = 1, $sampleRate = 1.0);

    /**
     * Counts one or more metrics.
     *
     * @param string|array $names
     * @param mixed        $value
     * @param array        $tags
     * @param float        $sampleRate
     * @return $this
     */
    public function counter($names, $value, array $tags = [], $sampleRate = 1.0);

    /**
     * Counts one or more metrics.
     * It is recommended to use number of milliseconds as value!
     *
     * @param string|array $names
     * @param int          $value
     * @param array        $tags
     * @param float        $sampleRate
     * @return $this
     */
    public function timer($names, $value, array $tags = [], $sampleRate = 1.0);

    /**
     * Counts one or more metrics.
     * It is recommended to use number of bytes as value!
     *
     * @param string|array $names
     * @param int          $value
     * @param array        $tags
     * @param float        $sampleRate
     * @return $this
     */
    public function memory($names, $value, array $tags = [], $sampleRate = 1.0);

    /**
     * Counts one or more metrics.
     *
     * @param string|array $names
     * @param int          $value
     * @param array        $tags
     * @return $this
     */
    public function gauge($names, $value, array $tags = []);

    /**
     * Counts one or more metrics.
     *
     * @param string|array $names
     * @param mixed        $value
     * @param array        $tags
     * @return $this
     */
    public function unique($names, $value, array $tags = []);
}