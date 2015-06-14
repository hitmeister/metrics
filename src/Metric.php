<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/13/15
 * Time: 10:12 PM
 */

namespace Hitmeister\Component\Metrics;

class Metric
{
    // Metric types
    const TYPE_COUNT = 'c';
    const TYPE_TIME  = 'ms';
    const TYPE_GAUGE = 'g';
    const TYPE_SET   = 's';

    // Time precision
    const PRECISION_NANOSECONDS  = 'n';
    const PRECISION_MICROSECONDS = 'u';
    const PRECISION_MILLISECONDS = 'ms';
    const PRECISION_SECONDS      = 's';
    const PRECISION_MINUTES      = 'm';
    const PRECISION_HOURS        = 'h';

    /**
     * Metric name.
     *
     * @var string
     */
    private $name = '';

    /**
     * Metric value. Usually it is `int` value of counter or `int`/`float` value of time.
     *
     * @var mixed
     */
    private $value;

    /**
     * @var float
     */
    private $sampleRate = 1.0;

    /**
     * Metric type. See types constants.
     *
     * @var int
     */
    private $type;

    /**
     * Time when the event occurred. Keep it null to put current time.
     *
     * @var int
     */
    private $time;

    /**
     * Time precision. See precision constants.
     *
     * @var string
     */
    private $precision = self::PRECISION_MICROSECONDS;

    /**
     * Additional tags. For example ['server' => 'web01', 'env' => 'prod'].
     * If handler does not support tags. They will be added to the name this way:
     * 'tag1_key.tag1_value.tag2_key.tag2_value.metric_name'
     *
     * @var array
     */
    private $tags = [];

    /**
     * @param string $name
     * @param mixed $value
     * @param string $type
     * @param array $tags
     */
    public function __construct($name, $value = 1, $type = self::TYPE_COUNT, array $tags = [])
    {
        $this->setName($name)->setValue($value)->setType($type)->setTags($tags);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Metric
     */
    public function setName($name)
    {
        $this->name = $this->sanitize($name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return Metric
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getSampleRate()
    {
        return $this->sampleRate;
    }

    /**
     * @param float $sampleRate Value from 0 to 1
     * @return Metric
     */
    public function setSampleRate($sampleRate)
    {
        if (1 != $sampleRate) {
            $sampleRate = min(1, max(0, $sampleRate));
        }
        $this->sampleRate = $sampleRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Metric
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return int
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param int|float $time
     * @param string $precision
     * @return Metric
     */
    public function setTime($time, $precision = self::PRECISION_MICROSECONDS)
    {
        $this->time = $time;
        $this->precision = $precision;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasTags()
    {
        return !empty($this->tags);
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     * @return Metric
     */
    public function setTags(array $tags)
    {
        if (!empty($tags)) {
            $tmp = [];
            foreach ($tags as $key => $value) {
                $tmp[$this->sanitize($key)] = $value;
            }
            $tags = $tmp;
            unset($tmp);
        }
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Metric
     */
    public function addTag($key, $value)
    {
        $this->tags[$this->sanitize($key)] = $value;
        return $this;
    }

    /**
     * @param array $tags
     * @return Metric
     */
    public function addTags(array $tags)
    {
        if (!empty($tags)) {
            foreach ($tags as $key => $value) {
                $this->tags[$this->sanitize($key)] = $value;
            }
        }
        return $this;
    }

    /**
     * @param string $key
     * @return Metric
     */
    public function removeTag($key)
    {
        $key = $this->sanitize($key);
        if (isset($this->tags[$key])) {
            unset($this->tags[$key]);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getTagsAsString()
    {
        if (empty($this->tags)) {
            return '';
        }

        $pairs = [];
        foreach ($this->tags as $key => $value) {
            // Keys already escaped.
            // Value should be sanitized only in case if we merge the tags into a string.
            $value = $this->sanitize($value);
            $pairs[] = "$key.$value";
        }

        return implode('.', $pairs);
    }

    /**
     * Sanitizes given string.
     * @param string $string
     * @return string
     */
    protected function sanitize($string)
    {
        return preg_replace('/[^a-zA-Z0-9_]/', '_', $string);
    }
}