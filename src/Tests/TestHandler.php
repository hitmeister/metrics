<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 9:27 PM
 */

namespace Hitmeister\Component\Metrics\Tests;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric;

class TestHandler implements HandlerInterface
{
    /** @var Metric[] */
    public $metrics = [];

    /**
     * @param Metric $metric
     */
    public function handle(Metric $metric)
    {
        $this->metrics[] = $metric;
    }

    /**
     * @param Metric[] $metrics
     */
    public function handleBatch(array $metrics)
    {
        $this->metrics = $metrics;
    }
}