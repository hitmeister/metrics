<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 11:37 PM
 */

namespace Hitmeister\Component\Metrics\Metric;

/**
 * Class DummyMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 * @codeCoverageIgnore
 */
class DummyMetric extends Metric
{
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return 'dummy';
    }
}