<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 14:50
 */

namespace Hitmeister\Component\Metrics\Tests\Buffer;

use Hitmeister\Component\Metrics\Buffer\ManualBuffer;

class ManualBufferTest extends BufferTestCase
{

	/**
	 * @var ManualBuffer
	 */
	private $buffer;

	/**
	 * @inheritdoc
	 */
	public function setUp()
	{
		parent::setUp();

		$this->buffer = new ManualBuffer();
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
		$metric->shouldReceive('touch');

		$this->buffer->add($metric);
		$this->assertCount(1, $this->buffer->getData());
	}

	/**
	 * Tests addBatch function
	 */
	public function testAddBatch()
	{
		$batch = $this->batchOfMockMetric();

		$this->buffer->addBatch($batch);
		$this->assertCount(count($batch), $this->buffer->getData());
	}

	/**
	 * Tests flush function
	 */
	public function testFlush()
	{
		$batch = $this->batchOfMockMetric();
		$this->buffer->addBatch($batch);

		$this->mockHandler->shouldReceive('handleBatch')->withArgs([$batch])->andReturn(true)->once();
		$this->buffer->flush();
		$this->assertCount(0, $this->buffer->getData());
	}

	/**
	 * @param int $count
	 * @return array
	 */
	protected function batchOfMockMetric($count = 2)
	{
		$batch = [];
		for ($i = 0; $i < $count; $i++) {
			$metric = $this->mockMetric();
			$metric->shouldReceive('touch');
			$batch[] = $metric;
		}
		return $batch;
	}
}