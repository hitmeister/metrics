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
}