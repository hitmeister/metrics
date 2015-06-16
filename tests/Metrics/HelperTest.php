<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 09:36
 */

namespace Hitmeister\Component\Metrics\Tests;

use Hitmeister\Component\Metrics\Helper;

class HelperTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Tests sanitize function
	 */
	public function testSanitize()
	{
		$values = [
			'normal_value' => 'normal_value',
			'should.be.sanitized' => 'should_be_sanitized',
			'Normal_CaseSensitive' => 'Normal_CaseSensitive',
			'S@n1TiZ3.M*.Hard' => 'S_n1TiZ3_M_Hard',
		];

		foreach ($values as $source => $result) {
			$sanitized = Helper::sanitize($source);
			$this->assertEquals($result, $sanitized);
		}
	}

	/**
	 * Tests mapAsString function
	 */
	public function testMapAsString()
	{
		$emptyMap = [];
		$this->assertEquals('', Helper::mapAsString($emptyMap));

		$mapNormal = [
			'name1' => 'value1',
			'name2' => 'value2',
		];
		$expected1 = 'name1.value1.name2.value2';
		$expected2 = 'name1:value1:name2:value2';
		$this->assertEquals($expected1, Helper::mapAsString($mapNormal));
		$this->assertEquals($expected2, Helper::mapAsString($mapNormal, true, ':'));

		$mapSanitize = [
			'name1' => 'should.be.sanitized',
			'name2' => 'S@n1TiZ3.M*.Hard',
		];
		$expected3 = 'name1.should_be_sanitized.name2.S_n1TiZ3_M_Hard';
		$expected4 = 'name1:should.be.sanitized:name2:S@n1TiZ3.M*.Hard';
		$this->assertEquals($expected3, Helper::mapAsString($mapSanitize));
		$this->assertEquals($expected4, Helper::mapAsString($mapSanitize, false, ':'));
	}

	/**
	 * Tests time tracking functions
	 */
	public function testTimerFunctions()
	{
		Helper::startTimer('timer1');
		usleep(1000);
		$timer1 = Helper::stopTimer('timer1');
		$this->assertGreaterThanOrEqual(1, $timer1);

		$timer2 = Helper::stopTimer('not_exist');
		$this->assertEquals(0, $timer2);
	}

	/**
	 * Test memory tracking functions
	 */
	public function testMemoryFunctions()
	{
		Helper::startTrackMemory('memory1');

		// Allocate memory
		$a = str_repeat('string', mt_rand(99999,9999999));

		$memory1 = Helper::stopTrackMemory('memory1');
		$this->assertGreaterThanOrEqual(1, $memory1);
		unset($a);

		$memory2 = Helper::stopTrackMemory('not_exist');
		$this->assertEquals(0, $memory2);
	}
}