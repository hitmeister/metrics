<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/14/15
 * Time: 1:47 PM
 */

namespace Hitmeister\Component\Metrics\Socket;

use Hitmeister\Component\Metrics\Exception;
use Socket\Raw\Socket as RawSocket;

class Socket extends RawSocket
{

    /**
     * @param string $buffer
     * @return int
     * @throws Exception
     */
    public function write($buffer)
    {
        $totalSent = 0;
        $length = strlen($buffer);

        while (true) {
            try {
                $sent = parent::write($buffer);
            } catch (\Exception $e) {
                throw new Exception($e->getMessage(), $e->getCode());
            }
            $totalSent += $sent;

            // Check if the entire message has been sent
            if ($sent >= $length) {
                break;
            }

            // If not sent the entire message.
            // Get the part of the message that has not yet been sent as message
            $buffer = substr($buffer, $sent);

            // Get the length of the not sent part
            $length -= $sent;
        }

        return $totalSent;
    }
}