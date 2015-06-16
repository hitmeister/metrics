<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 1:49 PM
 */

namespace Hitmeister\Component\Metrics\Socket;

use Socket\Raw\Exception;
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
	        throw Exception::createFromGlobalSocketOperation('Unable to create socket');
        }
        return new Socket($sock);
    }
}