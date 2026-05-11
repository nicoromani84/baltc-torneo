<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function asset_url($what) {
	return base_url().'static/'.$what;
}

function humanBytes($kb)
{
	$bytes = ( $kb * 1024 );
	if ($bytes >= 1073741824)
		$bytes = number_format($bytes / 1073741824, 2) . ' GB';

	elseif ($bytes >= 1048576)
		$bytes = number_format($bytes / 1048576, 2) . ' MB';

	elseif ($bytes >= 1024)
		$bytes = number_format($bytes / 1024, 2) . ' kB';

	elseif ($bytes > 1)
		$bytes = $bytes . ' bytes';

	elseif ($bytes == 1)
		$bytes = $bytes . ' byte';

	else
		$bytes = '0 bytes';

	return $bytes;
}