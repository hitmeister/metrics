<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 15:15
 */

// Tick use required
declare(ticks=1); // @codeCoverageIgnore

namespace Hitmeister\Component\Metrics\Buffer;

/**
 * @codeCoverageIgnore
 */
class OnShutdownBuffer extends ManualBuffer
{
	/**
	 * Creates new instance of OnShutdownBuffer
	 */
	public function __construct()
	{
		// Shutdown functions will not be executed if the process is killed with a SIGTERM or SIGKILL signal.
		// @see http://php.net/manual/en/function.register-shutdown-function.php
		register_shutdown_function([$this, 'flush']);

		if (function_exists('pcntl_signal')) {
			pcntl_signal(SIGTERM, [$this, 'flush']);
		}
	}
}