<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('human_filesize')) {

function human_filesize($bytes, $decimals = 2) {

	$size   = array('B','KB','MB','GB','TB','PB','EB','ZB','YB');
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f ",$bytes/pow(1024, $factor)).@$size[$factor];
}


function filenamer($str) {
	$separator = '-';

	$q_separator = preg_quote($separator, '#');

	$trans = array(
		'&+?;'			=> '',
		'[^\w\d _-]'		=> '',
		'\s+'			=> $separator,
		'('.$q_separator.')+'	=> $separator
	);

	$str = strip_tags($str);
	foreach ($trans as $key => $val)
	{
		$str = preg_replace('#'.$key.'#i'.(UTF8_ENABLED ? 'u' : ''), $val, $str);
	}


		$str = strtolower($str);

	return trim(trim($str, $separator));
}


}
