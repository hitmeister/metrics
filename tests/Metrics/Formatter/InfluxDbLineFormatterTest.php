<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 08:58
 */

namespace Formatter;

use Hitmeister\Component\Metrics\Formatter\InfluxDbLineFormatter;
use Hitmeister\Component\Metrics\Metric\CounterMetric;
use Hitmeister\Component\Metrics\Metric\GaugeMetric;

class InfluxDbLineFormatterTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var InfluxDbLineFormatter
	 */
	private $formatter;

	/**
	 * @inheritdoc
	 */
	public function setUp()
	{
		parent::setUp();

		$this->formatter = new InfluxDbLineFormatter();
	}

	/**
	 * @inheritdoc
	 */
	public function tearDown()
	{
		$this->formatter = null;

		parent::tearDown();
	}

	/**
	 * Tests gauge formatter
	 */
	public function testGaugeMetric()
	{
		$metric1 = new GaugeMetric('metric_name1', 10);
		$expected1 = 'metric_name1 value=10';
		$this->assertEquals($expected1, $this->formatter->format($metric1));

		$metric2 = new GaugeMetric('metric_name2', 40, ['env' => 'dev']);
		$expected2 = 'metric_name2,env=dev value=40';
		$this->assertEquals($expected2, $this->formatter->format($metric2));

		$metric3 = new GaugeMetric('metric_name3', ['internal' => 10]);
		$expected3 = 'metric_name3 internal=10';
		$this->assertEquals($expected3, $this->formatter->format($metric3));

		$metric4 = new GaugeMetric('metric_name4', '20');
		$expected4 = 'metric_name4 value=20';
		$this->assertEquals($expected4, $this->formatter->format($metric4));

		$metric5 = new GaugeMetric('metric_name5', '+20');
		$expected5 = 'metric_name5 value=20';
		$this->assertEquals($expected5, $this->formatter->format($metric5));

		$metric6 = new GaugeMetric('metric_name6', '-20');
		$expected6 = 'metric_name6 value=-20';
		$this->assertEquals($expected6, $this->formatter->format($metric6));

		$metric7 = new GaugeMetric('metric_name7', -20);
		$expected7 = 'metric_name7 value=-20';
		$this->assertEquals($expected7, $this->formatter->format($metric7));

		$metric8 = new GaugeMetric('metric_name8', ['internal' => 10], ['env' => 'dev']);
		$expected8 = 'metric_name8,env=dev internal=10';
		$this->assertEquals($expected8, $this->formatter->format($metric8));

		$now = time();
		$metric9 = new GaugeMetric('metric_name9', ['internal' => 10], ['env' => 'dev'], $now);
		$expected9 = 'metric_name9,env=dev internal=10 '.($now * 1000000);
		$this->assertEquals($expected9, $this->formatter->format($metric9));
	}

	/**
	 * Tests no value metric
	 */
	public function testNoValue()
	{
		$metric1 = new CounterMetric('metric_name1', []);
		$this->assertFalse($this->formatter->format($metric1));
	}

	/**
	 * Tests metric name quotation
	 */
	public function testMetricQuotation()
	{
		$metric1 = new CounterMetric('0metric_name1', 10);
		$expected1 = '"0metric_name1" value=10';
		$this->assertEquals($expected1, $this->formatter->format($metric1));

		$metric2 = new CounterMetric('metric_name2', 20, ['method/name' => 'Run/Me']);
		$expected2 = 'metric_name2,"method/name"=Run/Me value=20';
		$this->assertEquals($expected2, $this->formatter->format($metric2));

		$metric3 = new CounterMetric('metric_name3', 30, ['tag "with quotes" allowed' => 'tag_value']);
		$expected3 = 'metric_name3,"tag \"with quotes\" allowed"=tag_value value=30';
		$this->assertEquals($expected3, $this->formatter->format($metric3));

		$metric4 = new CounterMetric('metric_name4', ['error' => 'Hey, man! This is an error!']);
		$expected4 = 'metric_name4 error=Hey\,\ man!\ This\ is\ an\ error!';
		$this->assertEquals($expected4, $this->formatter->format($metric4));
	}

	/**
	 * Tests sampling
	 */
	public function testMetricSample()
	{
		$metric1 = new CounterMetric('metric_name1', 10);
		$metric1->setSampleRate(0.2);
		$expected1 = 'metric_name1 value='.(10*0.2);
		$this->assertEquals($expected1, $this->formatter->format($metric1));
	}
}