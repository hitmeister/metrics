<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 10:22
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Mockery as m;

abstract class SocketHandlerTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Socket|m\MockInterface
	 */
	protected $mockSocket;

	/**
	 * @var Factory|m\MockInterface
	 */
	protected $mockFactory;

	/**
	 * @var string
	 */
	protected $testHost = '127.0.0.1';

	/**
	 * @var int
	 */
	protected $testPort = 8125;

	/**
	 * @var int
	 */
	protected $testTimeout = 10;

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->mockSocket = m::mock('\Hitmeister\Component\Metrics\Socket\Socket');
		$this->mockSocket->shouldReceive('close')->once();
		$this->mockSocket->shouldReceive('connectTimeout')->withArgs([$this->testHost.':'.$this->testPort, $this->testTimeout])->once();

		$this->mockFactory = m::mock('\Hitmeister\Component\Metrics\Socket\Factory');
		$this->mockFactory->shouldReceive('createUdp4')->andReturn($this->mockSocket);
	}

	/**
	 * @inheritdoc
	 */
	protected function tearDown()
	{
		$this->mockSocket = null;
		$this->mockFactory = null;

		m::close();
		parent::tearDown();
	}
}