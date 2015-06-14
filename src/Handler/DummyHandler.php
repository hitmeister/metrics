<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/14/15
 * Time: 3:26 PM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Metric;

/**
 * @codeCoverageIgnore
 */
class DummyHandler implements HandlerInterface
{
    /**
     * @param Metric $metric
     */
    public function handle(Metric $metric) { }

    /**
     * @param Metric[] $metrics
     */
    public function handleBatch(array $metrics) { }
}
