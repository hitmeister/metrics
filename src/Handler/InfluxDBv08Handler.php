<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 15/06/15
 * Time: 11:43
 */

namespace Hitmeister\Component\Metrics\Handler;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Hitmeister\Component\Metrics\Exception;
use Hitmeister\Component\Metrics\Metric;

class InfluxDBv08Handler implements HandlerInterface
{
	/**
	 * @var Socket
	 */
	private $socket;

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var int
	 */
	private $apiTimeout = 5;

	/**
	 * @var
	 */
	private $apiEndpoint;

	/**
	 *
	 */
	public function __destruct()
	{
		if ($this->socket) {
			$this->socket->close();
		}
	}

	/**
	 * @param string  $host
	 * @param int     $port
	 * @param int     $timeout
	 * @param Factory $factory  Only for testing purpose
	 * @throws Exception
	 * @codeCoverageIgnore
	 */
	public function useUdp($host = '127.0.0.1', $port = 4444, $timeout = 5, Factory $factory = null)
	{
		if (null === $factory) {
			$factory = new Factory();
		}

		// Create socket
		$socket = $factory->createUdp4();

		// Connect
		try {
			$socket->connectTimeout($host.':'.$port, $timeout);
		} catch (\Exception $e) {
			$socket->close();
			throw new Exception('Unable connect to the socket', 0, $e);
		}

		$this->socket = $socket;
	}

	/**
	 * @param string          $host
	 * @param int             $port
	 * @param string          $dbName
	 * @param string          $username
	 * @param string          $password
	 * @param int             $timeout
	 * @param HandlerStack    $stack   Only for testing purpose
	 * @codeCoverageIgnore
	 */
	public function useRestApi(
		$host = '127.0.0.1', $port = 8086, $dbName ='metrics',
		$username = 'root', $password = 'root', $timeout = 5, HandlerStack $stack = null)
	{
		if (null === $stack) {
			$stack = HandlerStack::create();
		}

		$this->client = new Client(['handler' => $stack]);
		$this->apiTimeout = $timeout;
		$this->apiEndpoint = sprintf('http://%s:%d/db/%s/series?u=%s&p=%s', $host, $port, $dbName, $username, $password);
	}

	/**
	 * @param Metric $metric
	 */
	public function handle(Metric $metric)
	{
		// Not configured
		if (!$this->socket && !$this->client) {
			return;
		}

		$this->handleBatch([$metric]);
	}

	/**
	 * @param Metric[] $metrics
	 */
	public function handleBatch(array $metrics)
	{
		// Not configured
		if (!$this->socket && !$this->client) {
			return;
		}

		$series = [];
		$this->buildSeries($metrics, $series);
		$this->send(json_encode($series));
	}

	/**
	 * @param string $message
	 * @throws Exception
	 */
	protected function send($message)
	{
		if ($this->socket) {
			$this->socket->write($message);
		} elseif ($this->client) {
			$request = new Request('POST', $this->apiEndpoint, [], $message);
			$this->client->send($request, ['timeout' => $this->apiTimeout, 'verify' => false]);
		}
	}

	/**
	 * @param Metric[] $metrics
	 * @param array $series
	 * @return array
	 */
	private function buildSeries(array &$metrics, array &$series)
	{
		foreach ($metrics as $metric) {
			// Collect all series in one place
			$name = $metric->getName();
			if (!isset($series[$name])) {
				$series[$name] = [
					'name'    => $name,
					'columns' => [],
					'points'  => [],
				];
			}

			// Tags
			$hash = $metric->getTags();
			// Value
			$value = $metric->getValue();
			if (!is_array($value)) {
				$hash['value'] = $value;
			} else {
				$hash = array_merge($hash, $value);
			}
			// Time
			if ($metric->getTime()) {
				$hash['time'] = $this->adjustTime($metric->getTime(), $metric->getPrecision());
			}

			// Collect point
			$point = [];
			foreach ($hash as $hashKey => $hashValue) {
				// `key` => `index`
				if (!isset($series[$name]['columns'][$hashKey])) {
					$series[$name]['columns'][$hashKey] = count($series[$name]['columns']);
				}
				$point[$series[$name]['columns'][$hashKey]] = $hashValue;
			}
			$series[$name]['points'][] = $point;
		}

		// Fix columns
		foreach ($series as $name => &$item) {
			$item['columns'] = array_flip($item['columns']);
		}

		$series = array_values($series);
	}

	/**
	 * @param int $time
	 * @param string $precision
	 * @return int
	 */
	private function adjustTime($time, $precision)
	{
		// By default time precision is assumed to be milliseconds
		// @http://influxdb.com/docs/v0.8/api/reading_and_writing_data.html
		if ($precision == Metric::PRECISION_MILLISECONDS) {
			return $time;
		}

		switch($precision) {
			case Metric::PRECISION_NANOSECONDS:
				$time = (int)$time*0.000001;
				break;
			case Metric::PRECISION_MICROSECONDS:
				$time = (int)$time*0.001;
				break;
			case Metric::PRECISION_SECONDS:
				$time = (int)$time*1000;
				break;
			case Metric::PRECISION_MINUTES:
				$time = (int)$time*60000;
				break;
			case Metric::PRECISION_HOURS:
				$time = (int)$time*3600000;
				break;
		}

		return $time;
	}
}