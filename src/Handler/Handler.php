<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 18/06/15
 * Time: 10:03
 */

namespace Hitmeister\Component\Metrics\Handler;

use Hitmeister\Component\Metrics\Formatter\FormatterInterface;
use Hitmeister\Component\Metrics\Metric\Metric;

abstract class Handler implements HandlerInterface
{
	/**
	 * @var FormatterInterface
	 */
	protected $formatter;

	/**
	 * @inheritdoc
	 * @codeCoverageIgnore
	 */
	public function setFormatter(FormatterInterface $formatter)
	{
		$this->formatter = $formatter;
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
	abstract protected function send($message);

	/**
	 * Sends a batch of messages into the socket
	 *
	 * @param array $messages
	 * @return bool
	 */
	abstract protected function sendBatch(array $messages);
}