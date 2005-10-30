<?php
/**
 * TService_AJAX class file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Wei Zhuo. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author Wei Zhuo <weizhuo[at]gmail.com>
 * @version $Revision: 1.4 $  $Date: 2005/05/06 13:23:25 $
 * @package System.Web.Services
 */

require_once(dirname(__FILE__).'/AJAX/TAJAXServer.php');

/**
 * TService_AJAX class
 *
 * Allows XMLHTTPRequests for arbituary classes.
 * 
 * This service should be initialized from the application.spec file, e.g.
 * <pre>
 * <services>
 *    <service type="AJAX" RequestEncoding="PHP" >
 *       <class name="HelloService" />
 *    </service>
 * </services> 
 * </pre>
 *
 * @author Wei Zhuo <weizhuo[at]gmail.com>
 * @version v1.0, last update on 2005/03/11 21:44:52
 * @package System.Web.Services
 */
class TService_AJAX extends TService
{
	const service = '__AJAX';

	protected $server;
	
	function __construct($config)
	{
		$serverclass = 'TAJAXServer';
		if(isset($config['class'])) 
			$serverclass = (string)$config['class'];
		
		$this->server = new $serverclass(self::service);
		if(isset($config['RequestEncoding']))
			$this->server->RequestEncoding = strtolower($config['RequestEncoding']);

		foreach($config->class as $class)
		{
			$classname = $this->findClass((string)$class['name']);
			$this->server->addHandler(new $classname);
		}
	}
	
	function IsRequestServiceable($request)
	{
		return isset($request[self::service]);
	}
	
	function execute()
	{
		if (isset($_SERVER['QUERY_STRING']) 
			&& strcasecmp($_SERVER['QUERY_STRING'], self::service.'&client')==0) 
		{
			$this->server->displayClient();
		} 
		else 
		{
			// Include error handler - PHP errors, warnings and notices serialized to JS
			require_once JPSPAN . 'ErrorHandler.php';
			$this->server->serve();
		}		
	}
}

?>