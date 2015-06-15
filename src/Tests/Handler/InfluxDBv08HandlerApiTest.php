<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 15/06/15
 * Time: 13:44
 */

namespace Hitmeister\Component\Metrics\Tests\Handler;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Hitmeister\Component\Metrics\Handler\InfluxDBv08Handler;
use Hitmeister\Component\Metrics\Metric;
use Mockery as m;

class InfluxDBv08HandlerApiTest extends InfluxDBv08HandlerTestCase
{
	/**
	 * @var string
	 */
	private $testDbName = 'metrics';

	/**
	 * @var string
	 */
	private $testUsername = 'root';

	/**
	 * @var string
	 */
	private $testPassword = 'root';

	/**
	 * @var array
	 */
	private $historyContainer = [];

	/**
	 * @return InfluxDBv08Handler
	 */
	protected function getHandler()
	{
		$this->historyContainer = [];
		$mockResponses = [new Response(200)];

		$stack = HandlerStack::create(new MockHandler($mockResponses));
		$stack->push(Middleware::history($this->historyContainer));

		$handler = new InfluxDBv08Handler();
		$handler->useRestApi($this->testHost, $this->testPort, $this->testDbName, $this->testUsername, $this->testPassword, $this->testTimeout, $stack);
		return $handler;
	}

	/**
	 *
	 */
	public function testHandleOne()
	{
		list($metric, $expectedMessage) = $this->getMetricMessageOne();

		// Handle
		$handler = $this->getHandler();
		$handler->handle($metric);

		$this->assertCount(1, $this->historyContainer);

		$transaction = $this->extractLastResponse();
		$this->assertNotNull($transaction);

		if (null !== $transaction) {
			$uri = sprintf('http://%s:%d/db/%s/series?u=%s&p=%s', $this->testHost, $this->testPort, $this->testDbName, $this->testUsername, $this->testPassword);

			/** @var Request $request */
			list(, $request) = $transaction;
			$this->assertEquals('POST', $request->getMethod());
			$this->assertEquals($uri, (string)$request->getUri());
			$this->assertEquals($expectedMessage, (string)$request->getBody());
		}
	}

	/**
	 *
	 */
	public function testHandleBatch()
	{
		list($batch, $expectedMessage) = $this->getMetricMessageBatch();

		// Handle
		$handler = $this->getHandler();
		$handler->handleBatch($batch);

		$this->assertCount(1, $this->historyContainer);

		$transaction = $this->extractLastResponse();
		$this->assertNotNull($transaction);

		if (null !== $transaction) {
			/** @var Request $request */
			list(, $request) = $transaction;
			$this->assertEquals($expectedMessage, (string)$request->getBody());
		}
	}

	/**
	 * @return array|null
	 */
	private function extractLastResponse()
	{
		if (count($this->historyContainer) < 1) {
			return null;
		}

		$transaction = array_shift($this->historyContainer);
		if (!isset($transaction['response']) || !isset($transaction['request'])) {
			return null;
		}

		return [$transaction['response'], $transaction['request']];
	}
}