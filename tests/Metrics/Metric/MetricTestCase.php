<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 11:21
 */

namespace Hitmeister\Component\Metrics\Tests\Metric;

use Hitmeister\Component\Metrics\Metric\Metric;

abstract class MetricTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * Metric class name
	 *
	 * @var string
	 */
	protected $className = '';

	/**
	 * Provides test cases for create test
	 * @return array
	 */
	public function createProvider()
	{
		$metric1 = new $this->className('metric1', 1, [], null);
		$metric2 = new $this->className('metric2', ['external' => 25, 'internal' => 35], [], null);
		$metric3 = new $this->className('metric3', 10, ['env' => 'prod'], null);
		$metric4 = new $this->className('metric4', 10, [], 1234567890);

		return [
			[$metric1, ['metric1', ['value' => 1], [], null]],
			[$metric2, ['metric2', ['external' => 25, 'internal' => 35], [], null]],
			[$metric3, ['metric3', ['value' => 10], ['env' => 'prod'], null]],
			[$metric4, ['metric4', ['value' => 10], [], 1234567890]],
		];
	}

	/**
	 * Tests type function
	 */
	abstract public function testType();

	/**
	 * Test create function
	 *
	 * @dataProvider createProvider
	 * @param Metric $metric
	 * @param array  $expected
	 */
	public function testCreate(Metric $metric, array $expected)
	{
		list($name, $value, $tags, $time) = $expected;
		$this->assertEquals($name, $metric->getName());
		$this->assertEquals($value, $metric->getValue());
		$this->assertEquals($tags, $metric->getTags());
		$this->assertEquals($time, $metric->getTime());
	}

	/**
	 * Tests getters and setters
	 */
	public function testSetGet()
	{
		/** @var Metric $metric */
		$metric = new $this->className('metric', 1);

		// Create values
		$this->assertEquals('metric', $metric->getName());
		$this->assertEquals(['value' => 1], $metric->getValue());

		// Set get name
		$metric->setName('new_name');
		$this->assertEquals('new_name', $metric->getName());

		// Set get value
		$metric->setValue(['inside' => 10, 'outside' => 15]);
		$this->assertEquals(['inside' => 10, 'outside' => 15], $metric->getValue());

		$metric->setValue('string_value');
		$this->assertEquals(['value' => 'string_value'], $metric->getValue());

		// Set get touch time
		$this->assertNull($metric->getTime());
		$metric->touch();
		$this->assertNotNull($metric->getTime());
		$metric->setTime(1234567890);
		$this->assertEquals(1234567890, $metric->getTime());

		// Set get tags
		$this->assertFalse($metric->hasTags());
		$this->assertCount(0, $metric->getTags());
		$metric->setTags(['env' => 'prod']);
		$this->assertArrayHasKey('env', $metric->getTags());
	}
}