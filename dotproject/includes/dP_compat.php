<?php /* INCLUDES $Id$ */
##
## Global Compatibility Functions
##
if (!(defined('DP_BASE_DIR'))) {
	die('You should not access this file directly.');
}

//Checking for mb_* type functions ...
if (!function_exists('mb_internal_encoding')) {
	function mb_internal_encoding($encoding = null) {
		return (($encoding === null) ? 'UTF-8' :true);
	}
}

if (!function_exists('mb_convert_encoding')) {
	function mb_convert_encoding($str, $to_encoding, $from_encoding = null) {
		$from_encoding = (($from_encoding === null) ? mb_internal_encoding() : $from_encoding);
		return $str;
	}
}

if (!function_exists('mb_split')) {
	function mb_split($pattern, $string, $limit = -1) {
		# mb_split "patterns" are not PCRE patterns, so we need to
		# find a terminator that is unlikely to be in the string.
		$t = chr(1);
		return preg_split($t.$pattern.$t, $string, $limit);
	}
}

if (!function_exists('mb_stripos')) {
	function mb_stripos($haystack, $needle, $offset = 0, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return stripos($haystack, $needle, $offset);
	}
}

if (!function_exists('mb_stristr')) {
	function mb_stristr($haystack, $needle, $part = false, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			return stristr($haystack, $needle, $part);
		} else {
			return stristr($haystack, $needle);
		}
	}
}

if (!function_exists('mb_strlen')) {
	function mb_strlen($str, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return strlen($str);
	}
}

if (!function_exists('mb_strpos')) {
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return strpos($haystack, $needle, $offset);
	}
}

if (!function_exists('mb_strstr')) {
	function mb_strstr($haystack, $needle, $part = false, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			return strstr($haystack, $needle, $part);
		} else {
			return strstr($haystack, $needle);
		}
	}
}

if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return strtolower($str);
	}
}

if (!function_exists('mb_strtoupper')) {
	function mb_strtoupper($str, $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return strtoupper($str);
	}
}

if (!function_exists('mb_substr')) {
	function mb_substr($str, $start, $length = "undefined", $encoding = null) {
		$encoding = (($encoding === null) ? mb_internal_encoding() : $encoding);
		return (($length == "undefined") ? substr($str, $start) : substr($str, $start, $length));
	}
}
?>
