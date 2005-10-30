<?php

error_reporting(E_ALL);
restore_error_handler();

require_once(dirname(__FILE__).'/simpletest/unit_tester.php');
require_once(dirname(__FILE__).'/simpletest/web_tester.php');
require_once(dirname(__FILE__).'/simpletest/reporter.php');

class PradoTests
{
	public static function scriptDir()
	{
		return dirname(__FILE__).'/scripts/';
	}

	public static function examples($file='')
	{
		return 'http://'.$_SERVER['SERVER_NAME'].'/examples/'.$file;
	}

	public static function tests($file='')
	{
		return 'http://'.$_SERVER['SERVER_NAME'].'/tests/'.$file;
	}

}

?>