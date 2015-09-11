<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 9/11/15
 * Time: 9:43 PM
 */

namespace Hitmeister\Component\Metrics\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\Metric;
use Psr\Log\LoggerInterface;

/**
 * @codeCoverageIgnore
 */
class NullBuffer implements BufferInterface
{
    /**
     * @inheritdoc
     */
    public function getLogger()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setLogger(LoggerInterface $logger)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setHandler(HandlerInterface $handler)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function add(Metric $metric)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function addBatch(array $metrics)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        return true;
    }
}