<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/13/15
 * Time: 10:07 PM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Metric;

interface HandlerInterface
{
    /**
     * @param Metric $metric
     */
    public function handle(Metric $metric);

    /**
     * @param Metric[] $metrics
     */
    public function handleBatch(array $metrics);
}
