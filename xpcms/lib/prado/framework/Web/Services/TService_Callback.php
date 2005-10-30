<?php

require_once(dirname(__FILE__).'/AJAX/TCallbackServer.php');

class TService_Callback extends TService_AJAX
{	
	const service = '__CALLBACK';
	
	function IsRequestServiceable($request)
	{
		return false;
	}	
	
	function __construct($url)
	{
		$this->server = new TCallbackServer($url.self::service);
		$this->server->RequestEncoding = 'php';
	}
	
	public function isCallBack()
	{
		return !(isset($_SERVER['QUERY_STRING']) 
			&& strpos($_SERVER['QUERY_STRING'], self::service.'&client') !== false);
	}
	
	function execute()
	{
		if (!$this->isCallBack()) 
		{
			// Compress the Javascript
			//define('JPSPAN_INCLUDE_COMPRESS',TRUE);
			$this->server->displayClient();
		} 
		else 
		{
			// Include error handler - PHP errors, warnings and notices serialized to JS
			require_once JPSPAN . 'ErrorHandler.php';
			$this->server->serve();
		}		
	}	
	
	function addHandler($object, $description)
	{
		$this->server->addHandler($object, $description);
	}
	
	function setPostDataKeys($list)
	{
		$this->server->setPostDataKeys($list);
	}
	
	function loadCallBackPostData()
	{				
		$this->server->loadCallBackPostData();
	}
}

class TCallbackComponentDescription extends JPSpan_HandleDescription
{
	
}

class TCallbackEventParameter extends TEventParameter
{
	public $args;
}

?>