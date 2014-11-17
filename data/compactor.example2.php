<?php

	/**
	 * This example requires the minify javascript compacter library
	 * Freeley available from http://code.google.com/p/minify/
	 * Once download please unpack into the same directory as this file.
	 **/

	require_once 'compactor.php';
	
	function minify($code)
	{
    	require_once dirname(__FILE__).'/lib/jsmin.php';
    	return trim(JSMin::minify($code));
	}

	$compactor = new Compactor(array(
		'use_buffer'					=> true,
		'buffer_echo'					=> true,
		'compact_on_shutdown'			=> true,
		'compress_scripts'				=> true,
		'script_compression_callback'	=> 'minify'
	));
	
	echo file_get_contents('http://www.bbc.co.uk');
	exit;
