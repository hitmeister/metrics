<?php
/**
 * User: Maksim Naumov <fromyukki@gmail.com>
 * Date: 6/13/15
 * Time: 10:06 PM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;
use Hitmeister\Component\Metrics\Exception;
use Hitmeister\Component\Metrics\Metric;

class StatsDaemonHandler implements HandlerInterface
{
    /**
     * @var Socket
     */
    private $socket;

    /**
     * @var int
     */
    private $mtu = 1500;

    /**
     * @param string $host
     * @param int $port
     * @param int $timeout
     * @param string $scheme
     * @param Factory $factory Only for testing purpose
     * @throws Exception
     * @codeCoverageIgnore
     */
    public function __construct($host = '127.0.0.1', $port = 8125, $timeout = 5, $scheme = 'udp', Factory $factory = null)
    {
        if (null === $factory) {
            $factory = new Factory();
        }

        // Create socket
        $socket = ('tcp' === $scheme) ? $factory->createTcp4() : $factory->createUdp4();

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
     *
     */
    public function __destruct()
    {
        if ($this->socket) {
            $this->socket->close();
        }
    }

    /**
     * @param Metric $metric
     */
    public function handle(Metric $metric)
    {
        $this->handleBatch([$metric]);
    }

    /**
     * @param Metric[] $metrics
     */
    public function handleBatch(array $metrics)
    {
        $messages = [];

        // Go through the metrics
        foreach ($metrics as $metric) {
            $name = ($metric->hasTags() ? $metric->getTagsAsString().'.' : '') . $metric->getName();

            // Here we don't use the time fro metric, because the time will be adjusted by the stats daemon
            // For protocol information
            // @see https://github.com/etsy/statsd#usage
            $message = $name.':'.$metric->getValue().'|'.$metric->getType();

            if ($metric->getSampleRate() < 1) {
                $message .= '|@'.$metric->getSampleRate();
            }

            $messages[] = $message;
        }

        $this->sendBatch($messages);
    }

    /**
     * @param string $message
     * @throws Exception
     */
    protected function send($message)
    {
        $this->socket->write($message);
    }

    /**
     * @param array $messages
     */
    protected function sendBatch(array $messages)
    {
        $message = join("\n", $messages);
        if (strlen($message) <= $this->mtu) {
            $this->send($message);
            return;
        }

        $batches = $this->explodeByMtu($messages);
        foreach ($batches as $batch) {
            $this->send(join("\n", $batch));
        }
    }

    /**
     * @param array $messages
     * @return array
     */
    private function explodeByMtu(array $messages)
    {
        $index = 0;
        $chunks = [];
        $packageLength = 0;
        foreach ($messages as $message) {
            $messageLength = strlen($message);
            $nlCount = (isset($chunks[$index]) ? count($chunks[$index]) : 0);
            if ($messageLength + $packageLength + $nlCount > $this->mtu) {
                $index++;
                $packageLength = 0;
            }
            $chunks[$index][] = $message;
            $packageLength += $messageLength;
        }
        return $chunks;
    }
}