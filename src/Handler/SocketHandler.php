<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 10:11
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;

abstract class SocketHandler extends Handler
{
	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var int
	 */
	protected $port;

	/**
	 * @var int
	 */
	protected $timeout;

	/**
	 * @var string
	 */
	protected $scheme;

	/**
	 * @var int
	 */
	protected $mtu;

	/**
	 * @var Factory
	 */
	protected $factory;

	/**
	 * @var Socket
	 */
	protected $socket;

	/**
	 * Creates new instance of SocketHandler
	 *
	 * @param string $host
	 * @param int    $port
	 * @param int    $timeout
	 * @param string $scheme
	 * @param int    $mtu
	 */
	public function __construct($host, $port, $timeout, $scheme, $mtu = 1432)
	{
		$this->host = (string)$host;
		$this->port = (int)$port;
		$this->timeout = (int)$timeout;
		$this->scheme = (string)$scheme;
		$this->mtu = (int)$mtu;
	}

	/**
	 * Only for testing purpose
	 *
	 * @param Factory $factory
	 * @codeCoverageIgnore
	 */
	public function setFactory(Factory $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * Connects to the udp\tcp server
	 */
	public function connect()
	{
		// @codeCoverageIgnoreStart
		if (null !== $this->socket) {
			return;
		}
		if (null === $this->factory) {
			$this->factory = new Factory();
		}
		// @codeCoverageIgnoreEnd

		// Create socket
		$socket = ('tcp' === $this->scheme) ? $this->factory->createTcp4() : $this->factory->createUdp4();
		$socket->connectTimeout($this->host.':'.$this->port, $this->timeout);

		$this->socket = $socket;
	}

	/**
	 * Closes socket
	 */
	public function __destruct()
	{
		if ($this->socket) {
			$this->socket->close();
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function send($message)
	{
		$this->connect();
		$sent = $this->socket->write($message);
		return $sent == strlen($message);
	}

	/**
	 * @inheritdoc
	 */
	protected function sendBatch(array $messages)
	{
		$message = join("\n", $messages);
		if (strlen($message) <= $this->mtu) {
			return $this->send($message);
		}

		$result = true;
		$batches = self::explodeByMtu($messages, $this->mtu);
		foreach ($batches as $batch) {
			$result = $this->send(join("\n", $batch)) && $result;
		}
		return $result;
	}

	/**
	 * Be careful to keep the total length of the payload within your network's MTU.
	 * There is no single good value to use, but here are some guidelines for common network scenarios:
	 *
	 *  - Fast Ethernet (1432)     - This is most likely for Intranets.
	 *  - Gigabit Ethernet (8932)  - Jumbo frames can make use of this feature much more efficient.
	 *  - Commodity Internet (512) - If you are routing over the internet a value in this range will be reasonable.
	 *                               You might be able to go higher, but you are at the mercy of all the hops in your route.
	 *
	 * Note: These payload numbers take into account the maximum IP + UDP header sizes.
	 *
	 * @param array $messages
	 * @param int $mtu
	 * @return array
	 */
	public static function explodeByMtu(array $messages, $mtu = 1432)
	{
		$index = 0;
		$chunks = [];
		$packageLength = 0;

		foreach ($messages as $message) {
			$messageLength = strlen($message);
			$nlCount = isset($chunks[$index]) ? count($chunks[$index]) : 0;
			if ($messageLength + $packageLength + $nlCount > $mtu) {
				$index++;
				$packageLength = 0;
			}
			$chunks[$index][] = $message;
			$packageLength += $messageLength;
		}

		return $chunks;
	}
}