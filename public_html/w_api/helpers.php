<?php
/**
 * This file contains various helper functions.
 */

/**
 * Determine if $haystack starts with $needle.
 *
 * @param string $haystack
 * @param string $needle
 * @param bool $caseSensitive
 *
 * @return bool
 */
function starts_with($haystack, $needle, $caseSensitive = true)
{
	if (!$caseSensitive) {
		return stripos($haystack, $needle) === 0;
	}

	return strpos($haystack, $needle) === 0;
}

/**
 * Determine if $haystack starts with any of the given $needles.
 *
 * @param string $haystack
 * @param array $needles
 * @param bool $caseSensitive
 *
 * @return bool
 */
function starts_with_any($haystack, array $needles, $caseSensitive = true)
{
	foreach ($needles as $needle) {
		if (starts_with($haystack, $needle, $caseSensitive)) {
			return true;
		}
	}

	return false;
}
