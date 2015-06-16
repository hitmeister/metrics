<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 14:26
 */

namespace Hitmeister\Component\Metrics\Tests\Buffer;

use Hitmeister\Component\Metrics\Buffer\ImmediateBuffer;

class ImmediateBufferTest extends BufferTestCase
{
	/**
	 * @var ImmediateBuffer
	 */
	private $buffer;

	/**
	 * @inheritdoc
	 */
	public function setUp()
	{
		parent::setUp();

		$this->buffer = new ImmediateBuffer();
		$this->buffer->setHandler($this->mockHandler);
	}

	/**
	 * @inheritdoc
	 */
	public function tearDown()
	{
		$this->buffer = null;

		parent::tearDown();
	}

	/**
	 * Tests add function
	 */
	public function testAdd()
	{
		$metric = $this->mockMetric();
		$this->mockHandler->shouldReceive('handle')->withArgs([$metric])->andReturn(true)->once();
		$this->assertTrue($this->buffer->add($metric));
	}

	/**
	 * Tests add function with no handler
	 */
	public function testAddNoHandler()
	{
		$metric = $this->mockMetric();
		$this->buffer = new ImmediateBuffer();
		$this->assertFalse($this->buffer->add($metric));
	}

	/**
	 * Tests addBatch function
	 */
	public function testAddBatch()
	{
		$batch = [
			$this->mockMetric(),
			$this->mockMetric(),
		];

		$this->mockHandler->shouldReceive('handleBatch')->withArgs([$batch])->andReturn(true)->once();
		$this->assertTrue($this->buffer->addBatch($batch));
	}

	/**
	 * Tests addBatch function with no handler
	 */
	public function testAddBatchNoHandler()
	{
		$batch = [
			$this->mockMetric(),
			$this->mockMetric(),
		];

		$this->buffer = new ImmediateBuffer();
		$this->assertFalse($this->buffer->addBatch($batch));
	}
}