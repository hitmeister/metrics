<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 09:49
 */

namespace Hitmeister\Component\Metrics\Metric;

use Hitmeister\Component\Metrics\Helper;

abstract class Metric
{
	/**
	 * Metric name.
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * Metric value.
	 * This is array because some of metric storage supports multiple values.
	 * Example: `[external => 25, internal => 35]`.
	 * If storage supports only one value it will be in the `[value => 10]` field.
	 *
	 * @var array
	 */
	private $value = [];

	/**
	 * Time in milliseconds when the event occurred.
	 * Keep it null to have current time.
	 *
	 * @var int|null
	 */
	private $time;

	/**
	 * Metric tags.
	 * For example: env or server name.
	 *
	 * @var array
	 */
	private $tags = [];

	/**
	 * Creates new metric with given values
	 *
	 * @param string   $name
	 * @param mixed    $value
	 * @param array    $tags
	 * @param int|null $time    Time in milliseconds
	 */
	public function __construct($name, $value, array $tags = [], $time = null)
	{
		$this->setName($name)->setValue($value)->setTags($tags)->setTime($time);
	}

	/**
	 * Return metric type
	 *
	 * @return string
	 */
	abstract public function getType();

	/**
	 * Returns metric name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Sets metric name.
	 * The name will be sanitized using `Helper::sanitize` function.
	 *
	 * @param string $name
	 * @return $this
	 */
	public function setName($name)
	{
		$this->name = Helper::sanitize($name);
		return $this;
	}

	/**
	 * Returns metric value
	 *
	 * @return array
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * Sets metric value
	 *
	 * @param mixed $value
	 * @return $this
	 */
	public function setValue($value)
	{
		if (!is_array($value)) {
			$value = ['value' => $value];
		}
		$this->value = $value;
		return $this;
	}

	/**
	 * Returns metric time in milliseconds.
	 *
	 * @return int|null
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * Sets time in milliseconds.
	 * To reset time pul null here.
	 *
	 * @param int|null $time
	 * @return $this
	 */
	public function setTime($time) {
		$this->time = $time;
		return $this;
	}

	/**
	 * Sets current time in milliseconds.
	 *
	 * @return $this
	 */
	public function touch()
	{
		$this->time = (int)(microtime(true) * 1000);
		return $this;
	}

	/**
	 * Returns true if metric has tags
	 *
	 * @return bool
	 */
	public function hasTags()
	{
		return !empty($this->tags);
	}

	/**
	 * Returns tags
	 *
	 * @return array
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * Sets tags
	 *
	 * @param array $tags
	 * @return $this
	 */
	public function setTags(array $tags)
	{
		$this->tags = $tags;
		return $this;
	}
}