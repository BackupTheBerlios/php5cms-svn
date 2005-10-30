<?php

abstract class TService
{

	protected function findClass($classpath)
	{
		if(strpos($classpath, '.') !== false)
		{
			using($classpath);
			$class = explode('.', $classpath);
			return $class[count($class)-1];
		}
		else
			return $classpath;
	}
	
	abstract function IsRequestServiceable($request);
	
	abstract function execute();
}