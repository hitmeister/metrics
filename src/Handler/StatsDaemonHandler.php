<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/17/15
 * Time: 12:08 AM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Formatter\StatsDaemonFormatter;

class StatsDaemonHandler extends SocketHandler
{
    /**
     * Creates new instance of StatsDaemonHandler
     *
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param string $scheme
     * @param int $mtu
     */
    public function __construct($host = '127.0.0.1', $port = 8125, $timeout = 5, $scheme = 'udp', $mtu = 1432)
    {
	    parent::__construct($host, $port, $timeout, $scheme, $mtu);
        $this->formatter = new StatsDaemonFormatter();
    }
}