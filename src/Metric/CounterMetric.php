<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 10:31
 */

namespace Hitmeister\Component\Metrics\Metric;

use Hitmeister\Component\Metrics\Metric;

/**
 * Class CounterMetric
 *
 * @package Hitmeister\Component\Metrics\Metric
 */
class CounterMetric extends Metric
{
	/**
	 * @var float
	 */
	private $sampleRate = 1.0;

	/**
	 * @inheritdoc
	 */
	public function getType()
	{
		return 'counter';
	}

	/**
	 * Returns sample rate for value
	 *
	 * @return float
	 */
	public function getSampleRate()
	{
		return $this->sampleRate;
	}

	/**
	 * Sets sample rate for metric
	 *
	 * @param float $sampleRate
	 * @return $this
	 */
	public function setSampleRate($sampleRate)
	{
		if (1.0 != $sampleRate) {
			$sampleRate = min(1.0, max(0.0, $sampleRate));
		}
		$this->sampleRate = $sampleRate;
		return $this;
	}
}