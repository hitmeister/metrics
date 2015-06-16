<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 6/17/15
 * Time: 12:08 AM
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Formatter\FormatterInterface;
use Hitmeister\Component\Metrics\Formatter\StatsDaemonFormatter;
use Hitmeister\Component\Metrics\Metric\Metric;
use Hitmeister\Component\Metrics\Socket\Factory;
use Hitmeister\Component\Metrics\Socket\Socket;

class StatsDaemonHandler implements HandlerInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var string
     */
    private $scheme;

    /**
     * @var int
     */
    private $mtu;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Socket
     */
    private $socket;

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
        $this->host = (string)$host;
        $this->port = (int)$port;
        $this->timeout = (int)$timeout;
        $this->scheme = (string)$scheme;
        $this->mtu = (int)$mtu;
        $this->formatter = new StatsDaemonFormatter();
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
     * Sets formatter
     *
     * @param FormatterInterface $formatter
     * @codeCoverageIgnore
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
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
     * @inheritdoc
     */
    public function handle(Metric $metric)
    {
        $formatted = $this->formatter->format($metric);
        if (false === $formatted) {
            return false;
        }
        return $this->send($formatted);
    }

    /**
     * @inheritdoc
     */
    public function handleBatch(array $metrics)
    {
        $formatted = $this->formatter->formatBatch($metrics);
        if (empty($formatted)) {
            return false;
        }
        return $this->sendBatch($formatted);
    }

    /**
     * Sends message into the socket
     *
     * @param string $message
     * @return bool
     */
    protected function send($message)
    {
        $this->connect();
        $sent = $this->socket->write($message);
        return $sent == strlen($message);
    }

    /**
     * Sends a batch of messages into the socket
     *
     * @param array $messages
     * @return bool
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