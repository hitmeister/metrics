<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 15/06/15
 * Time: 13:44
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use Hitmeister\Component\Metrics\Handler\InfluxDBv08Handler;
use Hitmeister\Component\Metrics\Metric;
use Hitmeister\Component\Metrics\Socket\Socket;
use Mockery as m;

abstract class InfluxDBv08HandlerTestCase extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var Socket|m\MockInterface
	 */
	private $mockSocket;

	/**
	 * @var string
	 */
	protected $testHost = '127.0.0.1';

	/**
	 * @var int
	 */
	protected $testPort = 4444;

	/**
	 * @var int
	 */
	protected $testTimeout = 10;


	/**
	 *
	 */
	protected function tearDown()
	{
		m::close();

		parent::tearDown();
	}

	/**
	 * @return InfluxDBv08Handler
	 */
	abstract protected function getHandler();

	/**
	 *
	 */
	protected function getMetricMessageOne()
	{
		$metric = new Metric('metric_name', 10, Metric::TYPE_COUNT, ['env' => 'prod']);

		// Create expected message
		$expectedMessage = [['name' => $metric->getName(), 'columns' => ['env', 'value'], 'points' => [['prod', $metric->getValue()]]]];
		$expectedMessage = json_encode($expectedMessage);

		return [$metric, $expectedMessage];
	}

	/**
	 *
	 */
	protected function getMetricMessageBatch()
	{
		$batch = [
			new Metric('metric_name_batch1', 10),
			new Metric('metric_name_batch1', 20),
			new Metric('metric_name_batch3', 30),
			new Metric('metric_name_batch4', 40, Metric::TYPE_COUNT, ['env' => 'prod']),
		];

		// Build expected message
		$expectedMessage = [
			['name' => 'metric_name_batch1', 'columns' => ['value'], 'points' => [[10], [20]]],
			['name' => 'metric_name_batch3', 'columns' => ['value'], 'points' => [[30]]],
			['name' => 'metric_name_batch4', 'columns' => ['env', 'value'], 'points' => [['prod', 40]]]
		];
		$expectedMessage = json_encode($expectedMessage);

		return [$batch, $expectedMessage];
	}
}