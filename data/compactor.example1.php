<?php

	require_once 'compactor.php';

	$html = file_get_contents('http://www.bbc.co.uk');

	$size_before = mb_strlen($html, '8bit');
	
	$compactor = new Compactor(array(
		'buffer_echo' => false
	));
	$html = $compactor->squeeze($html);
	
	$size_after = mb_strlen($html, '8bit');
	
	echo 'With whitespace removed the size of the HTML file has been shrunk from '.round($size_before/1024, 2).'KB to '.round($size_after/1024, 2).'KB saving '.round((1-($size_after/$size_before))*100, 2).'%<br />
-----------------<br />
<br />'.$html;
	