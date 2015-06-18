<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 10:01
 */

namespace Hitmeister\Component\Metrics\Handler\InfluxDb;

use Hitmeister\Component\Metrics\Formatter\InfluxDbLineFormatter;
use Hitmeister\Component\Metrics\Handler\SocketHandler;

class UdpHandler extends SocketHandler
{
	/**
	 * Creates new instance of InfluxDbUdpHandler
	 *
	 * @param string $host
	 * @param int $port
	 * @param int $timeout
	 * @param int $mtu
	 */
	public function __construct($host = '127.0.0.1', $port = 4444, $timeout = 5, $mtu = 1432)
	{
		parent::__construct($host, $port, $timeout, 'udp', $mtu);
		$this->formatter = new InfluxDbLineFormatter();
	}
}