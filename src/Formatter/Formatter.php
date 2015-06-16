<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/16/15
 * Time: 10:34 PM
 */

namespace Hitmeister\Component\Metrics\Formatter;

use Hitmeister\Component\Metrics\Metric\Metric;

abstract class Formatter implements FormatterInterface
{
    /**
     * @inheritdoc
     */
    public function formatBatch(array $metrics)
    {
        $formatted = [];
        foreach ($metrics as $metric) {
            if ($metric instanceof Metric) {
                if (false !== ($result = $this->format($metric))) {
                    $formatted[] = $result;
                }
            }
        }
        return $formatted;
    }
}