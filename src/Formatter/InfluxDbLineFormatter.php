<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 17/06/15
 * Time: 15:50
 */

namespace Hitmeister\Component\Metrics\Formatter;

use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Metric\SamplingMetricInterface;

/**
 * Class InfluxDbLineFormatter
 *
 * @package Hitmeister\Component\Metrics\Formatter
 */
class InfluxDbLineFormatter extends Formatter
{
	/**
	 * @inheritdoc
	 */
	public function format(Metric $metric)
	{
		switch ($metric->getType()) {
			case 'gauge':
				return $this->processGauge($metric);
			default:
				return $this->process($metric);
		}
	}

	/**
	 * Process the metric.
	 *
	 * InfluxDB uses line protocol, which follows the following format:
	 * <measurement>[,<tag-key>=<tag-value>...] <field-key>=<field-value>[,<field2-key>=<field2-value>...] [unix-nano-timestamp]
	 *
	 * @param Metric $metric
	 * @return bool|string
	 */
	protected function process(Metric &$metric)
	{
		// By the InfluxDb rules we have to have at least one value
		$value = $metric->getValue();
		if (empty($value)) {
			return false;
		}

		// Sampling
		if ($metric instanceof SamplingMetricInterface && $metric->getSampleRate() < 1) {
			foreach ($value as $k => $v) {
				if (!is_string($v))
					$value[$k] = $v*$metric->getSampleRate();
			}
		}

		// Metric name
		$name = $this->quoteIdentifier($metric->getName());

		// Metric tags
		if ($metric->hasTags()) {
			$name .= ','.$this->arrayAsString($metric->getTags());
		}

		// Metric values
		$name .= ' '.$this->arrayAsString($value);

		// Metric time (from milliseconds to nanoseconds)
		if ($metric->getTime()) {
			$name .= ' '.($metric->getTime() * 1000000);
		}

		return $name;
	}

	/**
	 * Converts all gauge values to float
	 *
	 * @param Metric $metric
	 * @return bool|string
	 */
	protected function processGauge(Metric &$metric)
	{
		// All items should be int or float (currently values are: 10, +10, -10)
		$value = $metric->getValue();
		foreach ($value as $k => $v) {
			$value[$k] = (float)$v;
		}
		$metric->setValue($value);

		return $this->process($metric);
	}

	/**
	 * Converts tags to string
	 *
	 * @param array $tags
	 * @return string
	 */
	protected function arrayAsString(array $tags)
	{
		$pairs = [];
		foreach($tags as $key => $value) {
			$pairs[] = $this->quoteIdentifier($key).'='.$this->quoteValues($value);
		}
		return implode(',', $pairs);
	}

	/**
	 * Quotes identifiers.
	 * Measurements, tags, and field names containing any character other than (a-z,A-Z,0-9,_) or starting
	 * with a digit must be double-quoted.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function quoteIdentifier($string)
	{
		// First letter and then only a-z, 0-9 and '_'
		if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $string)) {
			return $string;
		}
		// All quotes should be escaped
		if (false !== strpos($string, '"')) {
			$string = str_replace('"', '\"', $string);
		}
		return '"'.$string.'"';
	}

	/**
	 * Quotes values
	 * @see https://github.com/influxdb/influxdb/issues/2980
	 *
	 * @param $string
	 * @return mixed
	 */
	protected function quoteValues($string)
	{
		if (!is_string($string)) {
			return $string;
		}
		// Quote space and comma
		return preg_replace('/([\s,])/', '\\\${1}', $string);
	}
}