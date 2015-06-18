<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 09:21
 */

namespace Hitmeister\Component\Metrics\Benchmarks\Formatter;

use Athletic\AthleticEvent;
use Hitmeister\Component\Metrics\Formatter\InfluxDbLineFormatter;
use Hitmeister\Component\Metrics\Metric\CounterMetric;

class InfluxDbLineEvent extends AthleticEvent
{
	/**
	 * @var InfluxDbLineFormatter
	 */
	private $formatter;

	/**
	 * @var CounterMetric
	 */
	private $metricName;

	/**
	 * @var CounterMetric
	 */
	private $metricTags;

	/**
	 * @var CounterMetric
	 */
	private $metricTagsSample;

	/**
	 * @inheritdoc
	 */
	protected function classSetUp()
	{
		$this->formatter = new InfluxDbLineFormatter();

		$this->metricName = new CounterMetric('metric_name', 10);

		$this->metricTags = new CounterMetric('metric_name', 10, ['env' => 'prod', 'server' => 'web01']);

		$this->metricTagsSample = new CounterMetric('metric_name', ['counter1' => 10, 'counter2' => 20], ['env' => 'production baby', 'server' => 'web01']);
		$this->metricTagsSample->setSampleRate(0.4);
	}

	/**
	 * @iterations 10000
	 */
	public function counterName()
	{
		$this->formatter->format($this->metricName);
	}

	/**
	 * @iterations 1000
	 */
	public function counterNameAndTags()
	{
		$this->formatter->format($this->metricTags);
	}

	/**
	 * @iterations 1000
	 */
	public function counterNameTagsAndSample()
	{
		$this->formatter->format($this->metricTagsSample);
	}
}