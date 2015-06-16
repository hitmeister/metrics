<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/13/15
 * Time: 10:07 PM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Metric;

interface HandlerInterface
{
    /**
     * @param Metric $metric
     * @return bool
     */
    public function handle(Metric $metric);

    /**
     * @param Metric[] $metrics
     * @return bool
     */
    public function handleBatch(array $metrics);
}
