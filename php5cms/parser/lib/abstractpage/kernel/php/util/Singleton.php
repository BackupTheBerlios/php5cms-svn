<?php

/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


define( 'SINGLETON_GLOBAL', 'SINGLETON' );


/**
 * Singleton class manager
 * 
 * This class makes it easy to instanciate and manipulate Singleton classes. 
 *
 * A Singleton class assures that only one object of the class exists at one time. 
 * Multiple attempts to create an object of a Singleton class return always the same class instance.
 *
 * Popular way to implemant singleton with PHP is like this.
 * 
 * Class Foo
 * {
 * 		function getInstance()
 * 		{
 * 			static $obj;
 * 		
 * 			if (! isset( $obj ) ) 
 * 				$obj = new Foo;
 * 			
 * 			return $obj;
 * 		}
 * }
 * 
 * $bar =& Foo::getInstance();
 * 
 * But this style have same problems.
 * 
 * 1) In every child class, You have to make getInstance function.
 * 2) call_user_func( 'foo', 'getInstance' ) do not work well. This is because PHP do not return the reference with call_user_func. 
 * 
 * 
 * How to Use
 * 
 * With this singleton class,
 * 	$bar =& Singleton::getInstance('foo');
 * is enough.
 * 
 * If you don't like class::method style call, 
 * 	$singleton =& Singleton::self
 * 	$bar =& $singleton->instance('foo');
 * is OK, too.
 * 
 * And you can manipulate your singleton class instance with global variable.
 * 	Singleton::init;
 * 	Global $SINGLETON;
 * 	$SINGLETON->instance('foo');
 * 	$SINGLETON->foo->method();
 * 
 * Singleton function like getInstance() is not necessary in the target class.
 * 
 * @package util
 */

class Singleton extends PEAR
{
	/**
	 * Alias of Singleton::self() for initialization porpose.
	 *
	 * @access public
	 */
	function init()
	{
		Singleton::self();
	}

	/**
	 * Get instance Singleton class itself.
	 *
	 * @access public
	 */
	function &self()
	{
		static $obj;
		
		if ( !isset( $obj ) )
		{
			$obj = new Singleton;
			
			if ( !isset( $GLOBALS[SINGLETON_GLOBAL] ) )
				$GLOBALS[SINGLETON_GLOBAL] =& $obj;
		}
		
		return $obj;
	}

	/**
	 * @param class name that you want to make Singleton.
	 * @access public
	 */
	function &getInstance( $name )
	{
		static $Singleton;
		
		if ( !isset( $Singleton ) )
			$Singleton =& Singleton::self();
            
		return $Singleton->instance( $name );
	}

	/**
	 * @param class name that you want to make Singleton.
	 * @access public
	 */
	function &instance( $name )
	{
	 	if ( !isset( $this->$name ) )
		{
			if( !class_exists( $name ) )
				return PEAR::raiseError( "Class does not exist." );
			
			$this->$name = new $name;
			
			if ( is_callable( array( $this->$name, 'getInstance' ) ) )
				$this->$name =& $this->$name->getInstance();
		}
		
		return $this->$name;
	}
} // END OF Singleton

?>
