<?php /* INCLUDES $Id$ */
##
## Global General Purpose Functions
##
if (!(defined('DP_BASE_DIR'))) {
	die('You should not access this file directly.');
}

//Checking for mb_* type functions ...
if (!function_exists('mb_internal_encoding')) {
	function mb_internal_encoding($encoding = null) {
		return (($encoding != null) ? true : 'UTF-8');
	}
}

if (!function_exists('mb_convert_encoding')) {
	function mb_convert_encoding($str, $to_encoding, $from_encoding = mb_internal_encoding()) {
		return $str;
	}
}

if (!function_exists('mb_split')) {
	function mb_split($pattern, $string, $limit = -1) {
		return preg_split($pattern, $string, $limit);
	}
}

if (!function_exists('mb_stripos')) {
	function mb_stripos($haystack, $needle, $offset = 0, $encoding = mb_internal_encoding()) {
		return stripos($haystack, $needle, $offset);
	}
}

if (!function_exists('mb_stristr')) {
	function mb_stristr($haystack, $needle, $part = false, $encoding = mb_internal_encoding()) {
		return stristr($haystack, $needle, $part);
	}
}

if (!function_exists('mb_strlen')) {
	function mb_strlen($str, $encoding = mb_internal_encoding()) {
		return strlen($str);
	}
}

if (!function_exists('mb_strpos')) {
	function mb_strpos($haystack, $needle, $offset = 0, $encoding = mb_internal_encoding()) {
		return strpos($haystack, $needle, $offset);
	}
}

if (!function_exists('mb_strstr')) {
	function mb_strstr($haystack, $needle, $part = false, $encoding = mb_internal_encoding()) {
		return strstr($haystack, $needle, $part);
	}
}

if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str, $encoding = mb_internal_encoding()) {
		return strtolower($str);
	}
}

if (!function_exists('mb_strtoupper')) {
	function mb_strtoupper($str, $encoding = mb_internal_encoding()) {
		return strtoupper($str);
	}
}

if (!function_exists('mb_substr')) {
	function mb_substr($str, $start, $length = "undefined", $encoding = mb_internal_encoding()) {
		return (($length == "undefined") ? substr($str, $start) : substr($str, $start, $length));
	}
}
?>