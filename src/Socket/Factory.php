<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/14/15
 * Time: 1:49 PM
 */

namespace Hitmeister\Component\Metrics\Socket;

use Hitmeister\Component\Metrics\Exception;
use Socket\Raw\Factory as BaseFactory;

class Factory extends BaseFactory
{
    /**
     * @param int $domain
     * @param int $type
     * @param int $protocol
     * @return Socket
     * @throws Exception
     */
    public function create($domain, $type, $protocol)
    {
        $sock = @socket_create($domain, $type, $protocol);
        if ($sock === false) {
            $code = socket_last_error();
            socket_clear_error();
            throw new Exception('Unable to create the socket: '.socket_strerror($code));
        }
        return new Socket($sock);
    }
}