<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/13/15
 * Time: 10:26 PM
 */

namespace Hitmeister\Component\Metrics;

use Hitmeister\Component\Metrics\Buffer\BufferInterface;
use Hitmeister\Component\Metrics\Buffer\ImmediateBuffer;
use Hitmeister\Component\Metrics\Collector\AbstractCollector;
use Hitmeister\Component\Metrics\Collector\CollectorInterface;
use Hitmeister\Component\Metrics\Handler\HandlerInterface;

class Collector extends AbstractCollector implements CollectorInterface
{
	/**
	 * Creates new instance of Collector
	 */
	public function __construct()
	{
		$this->buffer = new ImmediateBuffer();
	}

    /**
     * Returns metric prefix.
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Sets metric prefix.
     *
     * @param string $metricPrefix
     * @return $this
     */
    public function setPrefix($metricPrefix)
    {
        $this->prefix = (string)$metricPrefix;
        return $this;
    }


    /**
     * Returns true if collector hs tags
     *
     * @return bool
     */
    public function hasTags()
    {
        return !empty($this->tags);
    }

    /**
     * Returns tags.
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets tags.
     *
     * @param array $tags
     * @return $this
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * Adds tag.
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addTag($key, $value)
    {
        $this->tags[$key] = $value;
        return $this;
    }

    /**
     * Removes tag.
     *
     * @param string $key
     * @return $this
     */
    public function removeTag($key)
    {
        if (isset($this->tags[$key])) {
            unset($this->tags[$key]);
        }
        return $this;
    }

    /**
     * Returns buffer.
     *
     * @return BufferInterface
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Sets buffer.
     *
     * @param BufferInterface $buffer
     * @return $this
     */
    public function setBuffer(BufferInterface $buffer)
    {
        $this->buffer = $buffer;
        return $this;
    }

	/**
	 * Sets handler to buffer.
	 *
	 * @param HandlerInterface $handler
	 * @return $this
	 */
	public function setHandler(HandlerInterface $handler)
	{
		$this->buffer->setHandler($handler);
		return $this;
	}
}