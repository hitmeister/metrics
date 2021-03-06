<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 10:07 PM
 */

namespace Hitmeister\Component\Metrics\Formatter;

use Hitmeister\Component\Metrics\Metric\Metric;

interface FormatterInterface
{
    /**
     * @param Metric $metric
     * @return mixed|false
     */
    public function format(Metric $metric);

    /**
     * @param Metric[] $metrics
     * @return array
     */
    public function formatBatch(array $metrics);
}