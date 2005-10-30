<?php
/**
 * TPropertyNameInvalidException class file.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * Copyright(c) 2004 by Qiang Xue. All rights reserved.
 *
 * To contact the author write to {@link mailto:qiang.xue@gmail.com Qiang Xue}
 * The latest version of PRADO can be obtained from:
 * {@link http://prado.sourceforge.net/}
 *
 * @author Alex Flint <alex[at]linium[dor]net>
 * @version $Revision: 1.1 $  $Date: 2005/05/23 12:04:57 $
 * @package System.Exception
 */
 
/**
 * TPropertyNameInvalidException class
 *
 * TPropertyNameInvalidException is raised when the framework
 * detects a property is defined with an invalid name.
 *
 * Namespace: Exception
 *
 * @author Alex Flint <alex[at]linium[dor]net>
 * @version $Revision: 1.1 $  $Date: 2005/05/23 12:04:57 $
 * @package System.Exception
 */
class TPropertyNameClashException extends TException
{
	/**
	 * Constructor.
	 * @param string the component type
	 * @param string the property name
	 */
	function __construct($comType,$propName)
	{
		parent::__construct("Property name '$comType.$propName' clashes with a member variable of the same name.");
	}
}
?>
