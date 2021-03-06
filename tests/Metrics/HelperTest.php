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
}