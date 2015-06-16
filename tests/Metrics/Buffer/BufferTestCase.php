<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 14:50
 */

namespace Hitmeister\Component\Metrics\Tests\Buffer;

use Hitmeister\Component\Metrics\Handler\HandlerInterface;
use Hitmeister\Component\Metrics\Metric\AbstractMetric;
use Mockery as m;

abstract class BufferTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var m\MockInterface|HandlerInterface
	 */
	protected $mockHandler;

	/**
	 * @inheritdoc
	 */
	public function setUp()
	{
		parent::setUp();

		$this->mockHandler = m::mock('\Hitmeister\Component\Metrics\Handler\HandlerInterface');
	}

	/**
	 * @inheritdoc
	 */
	public function tearDown()
	{
		$this->mockHandler = null;

		m::close();
		parent::tearDown();
	}

	/**
	 * @return m\MockInterface|AbstractMetric
	 */
	protected function mockMetric()
	{
		return m::mock('\Hitmeister\Component\Metrics\Metric\AbstractMetric');
	}
}