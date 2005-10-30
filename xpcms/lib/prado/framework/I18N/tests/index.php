<?php

$mycachefile = (dirname(__FILE__).'/cache.txt');
$doTest = isset($_GET['test']) || !is_file($mycachefile);

//$doTest = true; //always do test

//point it to simpletest
if(!isset($_ENV['SIMPLETEST']))
{
	if (isset($_ENV['COMPUTERNAME']) && 
		($_ENV['COMPUTERNAME'] == 'JRAGSD00-C06' 
			|| $_ENV['COMPUTERNAME'] == 'MASTER')) 
	{
		$_ENV['SIMPLETEST'] = 'F:\simpletest';
		define('PRADO_BASE', 'C:\Program Files\Apache Group\Apache2\htdocs\prado\framework/');
	} 
	else 
	{
		$_ENV['SIMPLETEST'] = '/home/xlab6/simpletest';
		define('PRADO_BASE', '/home/xlab6/prado/framework/');
	}
}
else
{
	define('PRADO_BASE', 'f:/www/prado/framework/');
}

error_reporting(E_ALL);
restore_error_handler();

require_once($_ENV['SIMPLETEST'].'/unit_tester.php');
require_once($_ENV['SIMPLETEST'].'/reporter.php');
require_once(dirname(__FILE__).'/HtmlReporterWithCoverage.php');


define('FRAMEWORK_BASE', realpath(dirname(__FILE__).'/../core/'));

if(isset($_GET['file']))
{
	$filename = rawurldecode($_GET['file']);
	$file = realpath(FRAMEWORK_BASE.$filename);
	if(is_int(strpos($file,FRAMEWORK_BASE)) && is_file($file))
	{
		$coverage = new HTMLCoverageReport($file, $filename, explode(',',$_GET['lines']));
		$coverage->show();
	}
	else
	{
		echo 'Access Denied!';
	}
	die();
}

if($doTest)
{
	ob_start();

	$test = new GroupTest('All tests');

	$dir = dir(dirname(__FILE__));

	while (false !== ($entry = $dir->read()))
	{
		if(is_file($entry) && strtolower(substr($entry,0,4)) == 'test')
		{
			if(strpos(strtolower($entry), 'mysql'))
			{
				if(extension_loaded('mysql'))
				$test->addTestFile($entry);
			}
			else
			$test->addTestFile($entry);
		}
	}
	$dir->close();

	$sapi = php_sapi_name();

	if ($sapi == 'cli' || $sapi == 'cgi-fcgi')
	{
		exit ($test->run(new TextReporter()) ? 0 : 1);
	}

	$test->run(new HtmlReporterWithCoverage('index.php', FRAMEWORK_BASE));

	file_put_contents($mycachefile, ob_get_contents());
}
else
{
	echo file_get_contents($mycachefile);
}
?>