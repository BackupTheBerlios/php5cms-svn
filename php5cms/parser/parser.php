<?php
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/classes' . PATH_SEPARATOR . dirname(__FILE__) . '/lib');

function import($package) {
	
	$file  = str_replace('.', '/', $package) . '.php';
	
	include_once $file;
	
}

require_once 'abstractpage/prepend.php';

import('p5c.Config');
import('p5c.FilterChain');

import('p5c.filter.FrontController');
import('p5c.filter.LanguageFilter');

import('p5c.http.Request');
import('p5c.http.Response');


$chain = new p5c_FilterChain(
	new p5c_filter_LanguageFilter(),
	new p5c_filter_FrontController()
);
$chain->execute(p5c_http_Request::getInstance(), p5c_http_Response::getInstance());
?>
