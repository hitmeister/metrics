<?php
/**
 * Created for Hitmeister Project.
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 16/06/15
 * Time: 09:26
 */

namespace Hitmeister\Component\Metrics;

class Helper
{
	/**
	 * List of timers
	 *
	 * @var array
	 */
	private static $timers = [];

	/**
	 * List of memory usages
	 *
	 * @var array
	 */
	private static $memoryUsages = [];

	/**
	 * Sanitize string.
	 *
	 * @param string $string
	 * @return string
	 */
	public static function sanitize($string)
	{
		return preg_replace('/[^a-zA-Z0-9_]+/', '_', $string);
	}

	/**
	 * @param array  $map
	 * @param bool   $sanitize
	 * @param string $delimiter
	 * @return string
	 */
	public static function mapAsString($map, $sanitize = true, $delimiter = '.')
	{
		if (empty($map)) {
			return '';
		}

		$pairs = [];
		foreach ($map as $key => $value) {
			if ($sanitize) {
				$key = self::sanitize($key);
				$value = self::sanitize($value);
			}
			$pairs[] = $key.$delimiter.$value;
		}
		return implode($delimiter, $pairs);
	}

	/**
	 * Starts timer
	 *
	 * @param string $name
	 */
	public static function startTimer($name)
	{
		self::$timers[$name] = microtime(true) * 1000;
	}

	/**
	 * Stops timer and returns number of milliseconds elapsed from start
	 *
	 * @param string $name
	 * @return int
	 */
	public static function stopTimer($name)
	{
		if (!isset(self::$timers[$name])) {
			return 0;
		}
		$time = (microtime(true) * 1000) - self::$timers[$name];

		unset(self::$timers[$name]);
		return (int)$time;
	}

	/**
	 * Saves current memory usage
	 *
	 * @param string $name
	 */
	public static function startTrackMemory($name)
	{
		self::$memoryUsages[$name] = memory_get_usage(true);
	}

	/**
	 * Returns number of bytes consumed from start
	 *
	 * @param string $name
	 * @return int
	 */
	public static function stopTrackMemory($name)
	{
		if (!isset(self::$memoryUsages[$name])) {
			return 0;
		}
		$memory = memory_get_usage(true) - self::$memoryUsages[$name];

		unset(self::$memoryUsages[$name]);
		return $memory;
	}
}